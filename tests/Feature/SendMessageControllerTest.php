<?php

use App\Models\Contact;
use App\Models\Message;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;

uses(RefreshDatabase::class);

beforeEach(function () {
    Config::set('services.api.secret_token', 'test-api-secret-123');
});

test('returns 202 accepted response with correct json structure when valid payload is sent', function () {
    $payload = [
        'phone_number' => '5561999999999',
        'message' => 'Hello from controller test!',
    ];

    $response = $this->withHeaders([
        'X-Api-Secret' => 'test-api-secret-123',
        'Accept' => 'application/json',
    ])->postJson('/api/messages/send', $payload);

    $response->assertStatus(202)
        ->assertJson([
            'status' => 'queued',
            'message' => 'Message queued for sending.',
            'data' => [
                'status' => 'pending',
            ],
        ])
        ->assertJsonStructure([
            'status',
            'message',
            'data' => [
                'message_id',
                'contact_id',
                'status',
            ],
        ]);
});

test('creates contact and message records in database on successful request', function () {
    $payload = [
        'phone_number' => '5561888888888',
        'message' => 'Testing database persistence',
    ];

    $this->withHeaders([
        'X-Api-Secret' => 'test-api-secret-123',
    ])->postJson('/api/messages/send', $payload);

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
});

test('associates message with existing contact if contact already exists in database', function () {
    $existingContact = Contact::create([
        'wa_id' => '5561777777777',
        'name' => 'John Doe',
        'phone_number' => '5561777777777',
        'last_message_at' => now()->subDay(),
    ]);

    $payload = [
        'phone_number' => '5561777777777',
        'message' => 'Message to existing contact',
    ];

    $response = $this->withHeaders([
        'X-Api-Secret' => 'test-api-secret-123',
    ])->postJson('/api/messages/send', $payload);

    $response->assertStatus(202)
        ->assertJson([
            'data' => [
                'contact_id' => $existingContact->id,
            ],
        ]);

    $this->assertDatabaseCount('contacts', 1);
    $this->assertDatabaseCount('messages', 1);
});

test('returns 401 unauthorized when X-Api-Secret header is missing or incorrect', function () {
    $payload = [
        'phone_number' => '5561999999999',
        'message' => 'Unauthorized attempt',
    ];

    $this->postJson('/api/messages/send', $payload)
        ->assertStatus(401);

    $this->withHeaders(['X-Api-Secret' => 'wrong-secret'])
        ->postJson('/api/messages/send', $payload)
        ->assertStatus(401);

    $this->assertDatabaseCount('messages', 0);
});

test('returns 422 unprocessable content when request validation fails', function () {
    $response = $this->withHeaders([
        'X-Api-Secret' => 'test-api-secret-123',
        'Accept' => 'application/json',
    ])->postJson('/api/messages/send', [
        'phone_number' => '123',
        'message' => '',
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['phone_number', 'message']);

    $this->assertDatabaseCount('messages', 0);
});