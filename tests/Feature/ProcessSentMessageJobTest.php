<?php

use App\Jobs\ProcessSentMessage;
use App\Models\Contact;
use App\Models\Message;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Str;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    Config::set('services.whatsapp.phone_number_id', 'test-api-secret-123');
    Config::set('services.whatsapp.access_token', 'test-api-secret-123');
});

test('updates message status and wamid on successful api call', function () {
    Http::fake([
        '://graph.facebook.com*' => Http::response(['messages' => array(['id' => 'wamid.TEST_ID_12345'])], 200),
    ]);

    $contact = Contact::create([
        'wa_id' => '5561999999999',
        'name' => 'John Doe',
        'phone_number' => '5561999999999',
    ]);

    $tempWamid = 'outbound_temp_' . Str::uuid();

    $message = Message::create([
        'contact_id' => $contact->id,
        'wamid'      => $tempWamid,
        'direction'  => 'outbound',
        'type'       => 'text',
        'body'       => 'Hello from Pest test!',
        'timestamp'  => '1700000000',
        'status'     => 'pending',
    ]);

    (new ProcessSentMessage($message))->handle();

    $this->assertDatabaseHas('messages', [
        'wamid' => 'wamid.TEST_ID_12345',
        'direction' => 'outbound',
        'type' => 'text',
        'body' => 'Hello from Pest test!',
        'status' => 'sending',
    ]);
});

test('updates message status on failed api call', function () {
    Http::fake([
        '://graph.facebook.com*' => Http::response(['error' => 'cannot sent message'], 400),
    ]);

    $contact = Contact::create([
        'wa_id' => '5561999999999',
        'name' => 'John Doe',
        'phone_number' => '5561999999999',
    ]);

    $tempWamid = 'outbound_temp_' . Str::uuid();

    $message = Message::create([
        'contact_id' => $contact->id,
        'wamid'      => $tempWamid,
        'direction'  => 'outbound',
        'type'       => 'text',
        'body'       => 'Hello from Pest test!',
        'timestamp'  => '1700000000',
        'status'     => 'pending',
    ]);

    (new ProcessSentMessage($message))->handle();

    $this->assertDatabaseHas('messages', [
        'wamid' => $tempWamid,
        'direction' => 'outbound',
        'type' => 'text',
        'body' => 'Hello from Pest test!',
        'status' => 'failed',
    ]);
});

test('checks if template type is sent when last message from contact is outside 24 hours window', function () {
    Http::fake([
        '://graph.facebook.com*' => Http::response(['messages' => array(['id' => 'wamid.TEST_ID_12345'])], 200),
    ]);

    $contact = Contact::create([
        'wa_id' => '5561999999999',
        'name' => 'John Doe',
        'phone_number' => '5561999999999',
        'last_message_from_contact_at' => now()->subHours(25),
    ]);

    $tempWamid = 'outbound_temp_' . Str::uuid();

    $message = Message::create([
        'contact_id' => $contact->id,
        'wamid'      => $tempWamid,
        'direction'  => 'outbound',
        'type'       => 'text',
        'body'       => 'Hello from Pest test!',
        'timestamp'  => '1700000000',
        'status'     => 'pending',
    ]);

    (new ProcessSentMessage($message))->handle();

    $this->assertDatabaseHas('messages', [
        'wamid' => 'wamid.TEST_ID_12345',
        'direction' => 'outbound',
        'type' => 'text',
        'body' => 'Hello from Pest test!',
        'status' => 'sending',
    ]);

    Http::assertSent(function (Request $request) {
        return $request['type'] === 'template';
    });
});

test('checks if text type is sent when last message from contact is inside 24 hours window', function () {
    Http::fake([
        '://graph.facebook.com*' => Http::response(['messages' => array(['id' => 'wamid.TEST_ID_12345'])], 200),
    ]);

    $contact = Contact::create([
        'wa_id' => '5561999999999',
        'name' => 'John Doe',
        'phone_number' => '5561999999999',
        'last_message_from_contact_at' => now()->subHours(5),
    ]);

    $tempWamid = 'outbound_temp_' . Str::uuid();

    $message = Message::create([
        'contact_id' => $contact->id,
        'wamid'      => $tempWamid,
        'direction'  => 'outbound',
        'type'       => 'text',
        'body'       => 'Hello from Pest test!',
        'timestamp'  => '1700000000',
        'status'     => 'pending',
    ]);

    (new ProcessSentMessage($message))->handle();

    $this->assertDatabaseHas('messages', [
        'wamid' => "wamid.TEST_ID_12345",
        'direction' => 'outbound',
        'type' => 'text',
        'body' => 'Hello from Pest test!',
        'status' => 'sending',
    ]);

    Http::assertSent(function (Request $request) {
        return $request['type'] === 'text';
    });
});