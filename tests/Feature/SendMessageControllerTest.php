<?php

use App\Jobs\ProcessSentMessage;
use App\Models\Contact;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Queue;

uses(RefreshDatabase::class);

beforeEach(function () {
    Config::set('services.api.secret_token', 'test-api-secret-123');
});

test('creates contact and text message record on successful send request', function () {
    Queue::fake();

    $payload = [
        'phone_number' => '5561888888888',
        'message' => 'Testing database persistence',
    ];

    $response = $this->withHeaders([
        'X-Api-Secret' => 'test-api-secret-123',
    ])->postJson('/api/messages/send', $payload);

    $response->assertStatus(202)
        ->assertJson([
            'status' => 'queued',
            'message' => 'Message queued for sending.',
        ]);

    $this->assertDatabaseHas('contacts', [
        'wa_id' => '5561888888888',
        'phone_number' => '5561888888888',
    ]);

    $this->assertDatabaseHas('messages', [
        'direction' => 'outbound',
        'type' => 'text',
        'body' => 'Testing database persistence',
        'status' => 'pending',
    ]);

    Queue::assertPushed(ProcessSentMessage::class, function ($job) {
        return $job->message->body === 'Testing database persistence' && $job->message->type === 'text';
    });
});

test('creates template message record and dispatches job on sendTemplate request', function () {
    Queue::fake();

    $payload = [
        'phone_number' => '5561999999999',
        'template_name' => 'order_update',
        'language_code' => 'en_US',
        'components' => [
            [
                'type' => 'body',
                'parameters' => [
                    ['type' => 'text', 'text' => 'John Doe'],
                ],
            ],
        ],
    ];

    $response = $this->withHeaders([
        'X-Api-Secret' => 'test-api-secret-123',
    ])->postJson('/api/messages/sendTemplate', $payload);

    $response->assertStatus(202)
        ->assertJson([
            'status' => 'queued',
            'message' => 'Message queued for sending.',
        ]);

    $this->assertDatabaseHas('messages', [
        'direction' => 'outbound',
        'type' => 'template',
        'body' => 'Template: order_update',
        'status' => 'pending',
    ]);

    Queue::assertPushed(ProcessSentMessage::class, function ($job) {
        return $job->message->type === 'template' &&
               $job->message->payload['name'] === 'order_update';
    });
});

test('associates message with existing contact if contact exists', function () {
    Queue::fake();

    $existingContact = Contact::create([
        'wa_id' => '5561777777777',
        'name' => 'John Doe',
        'phone_number' => '5561777777777',
        'last_message_from_contact_at' => now()->subDay(),
    ]);

    $payload = [
        'phone_number' => '5561777777777',
        'message' => 'Message to existing contact',
    ];

    $response = $this->withHeaders([
        'X-Api-Secret' => 'test-api-secret-123',
    ])->postJson('/api/messages/send', $payload);

    $response->assertJson([
        'data' => [
            'contact_id' => $existingContact->id,
        ],
    ]);

    $this->assertDatabaseCount('contacts', 1);
    $this->assertDatabaseCount('messages', 1);
});

test('returns 401 unauthorized when X-Api-Secret header is invalid', function () {
    Queue::fake();

    $payload = [
        'phone_number' => '5561999999999',
        'message' => 'Unauthorized attempt',
    ];

    $this->postJson('/api/messages/send', $payload)->assertStatus(401);

    $this->withHeaders(['X-Api-Secret' => 'wrong-secret'])
        ->postJson('/api/messages/send', $payload)
        ->assertStatus(401);

    $this->assertDatabaseCount('messages', 0);
});

test('returns 422 unprocessable entity when text or template request validation fails', function () {
    Queue::fake();

    $response = $this->withHeaders([
        'X-Api-Secret' => 'test-api-secret-123',
        'Accept' => 'application/json',
    ])->postJson('/api/messages/send', [
        'phone_number' => '',
        'message' => '',
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['phone_number', 'message']);

    $templateResponse = $this->withHeaders([
        'X-Api-Secret' => 'test-api-secret-123',
        'Accept' => 'application/json',
    ])->postJson('/api/messages/sendTemplate', [
        'phone_number' => '5561999999999',
        // missing required template_name
    ]);

    $templateResponse->assertStatus(422)
        ->assertJsonValidationErrors(['template_name']);

    $this->assertDatabaseCount('messages', 0);
});