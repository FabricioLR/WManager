<?php

use App\Jobs\ProcessSentMessage;
use App\Models\Contact;
use App\Models\Message;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    Config::set('services.whatsapp.phone_number_id', '123456789');
    Config::set('services.whatsapp.access_token', 'test-token-xyz');
    Config::set('services.whatsapp.api_version', 'v20.0');
});

test('updates text message status to sending and updates wamid on successful api call inside 24h window', function () {
    Http::fake([
        'https://graph.facebook.com/*' => Http::response(['messages' => [['id' => 'wamid.TEST_ID_12345']]], 200),
    ]);

    $contact = Contact::create([
        'wa_id' => '5561999999999',
        'name' => 'John Doe',
        'phone_number' => '5561999999999',
        'last_message_from_contact_at' => now()->subHour(),
    ]);

    $tempWamid = 'outbound_temp_' . Str::uuid();

    $message = Message::create([
        'contact_id' => $contact->id,
        'wamid'      => $tempWamid,
        'direction'  => 'outbound',
        'type'       => 'text',
        'body'       => 'Hello from Pest test!',
        'timestamp'  => now(),
        'status'     => 'pending',
    ]);

    (new ProcessSentMessage($message))->handle();

    $this->assertDatabaseHas('messages', [
        'id'     => $message->id,
        'wamid'  => 'wamid.TEST_ID_12345',
        'status' => 'sending',
    ]);

    Http::assertSent(function (Request $request) {
        return $request['type'] === 'text' && 
               $request['text']['body'] === 'Hello from Pest test!';
    });
});

test('fails text message if contact is outside 24h window', function () {
    Http::fake();

    $contact = Contact::create([
        'wa_id' => '5561999999999',
        'name' => 'John Doe',
        'phone_number' => '5561999999999',
        'last_message_from_contact_at' => now()->subHours(25),
    ]);

    $message = Message::create([
        'contact_id' => $contact->id,
        'wamid'      => 'outbound_temp_' . Str::uuid(),
        'direction'  => 'outbound',
        'type'       => 'text',
        'body'       => 'Outside 24h text',
        'timestamp'  => now(),
        'status'     => 'pending',
    ]);

    (new ProcessSentMessage($message))->handle();

    $this->assertDatabaseHas('messages', [
        'id'     => $message->id,
        'status' => 'failed',
    ]);

    Http::assertNothingSent();
});

test('sends template payload successfully regardless of 24h window', function () {
    Http::fake([
        'https://graph.facebook.com/*' => Http::response(['messages' => [['id' => 'wamid.TEMPLATE_ID_123']]], 200),
    ]);

    $contact = Contact::create([
        'wa_id' => '5561999999999',
        'name' => 'John Doe',
        'phone_number' => '5561999999999',
        'last_message_from_contact_at' => now()->subDays(5), // Way outside window
    ]);

    $templatePayload = [
        'name' => 'hello_world',
        'language' => ['code' => 'en_US'],
        'components' => [],
    ];

    $message = Message::create([
        'contact_id' => $contact->id,
        'wamid'      => 'outbound_temp_' . Str::uuid(),
        'direction'  => 'outbound',
        'type'       => 'template',
        'body'       => 'Template: hello_world',
        'payload'    => $templatePayload,
        'timestamp'  => now(),
        'status'     => 'pending',
    ]);

    (new ProcessSentMessage($message))->handle();

    $this->assertDatabaseHas('messages', [
        'id'     => $message->id,
        'wamid'  => 'wamid.TEMPLATE_ID_123',
        'status' => 'sending',
    ]);

    Http::assertSent(function (Request $request) use ($templatePayload) {
        return $request['type'] === 'template' &&
               $request['template']['name'] === $templatePayload['name'];
    });
});

test('updates message status to failed on API error response', function () {
    Http::fake([
        'https://graph.facebook.com/*' => Http::response(['error' => 'API error'], 400),
    ]);

    $contact = Contact::create([
        'wa_id' => '5561999999999',
        'name' => 'John Doe',
        'phone_number' => '5561999999999',
        'last_message_from_contact_at' => now()->subMinutes(10),
    ]);

    $tempWamid = 'outbound_temp_' . Str::uuid();

    $message = Message::create([
        'contact_id' => $contact->id,
        'wamid'      => $tempWamid,
        'direction'  => 'outbound',
        'type'       => 'text',
        'body'       => 'Hello',
        'timestamp'  => now(),
        'status'     => 'pending',
    ]);

    (new ProcessSentMessage($message))->handle();

    $this->assertDatabaseHas('messages', [
        'id'     => $message->id,
        'wamid'  => $tempWamid,
        'status' => 'failed',
    ]);
});