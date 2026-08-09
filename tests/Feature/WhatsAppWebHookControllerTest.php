<?php

use App\Jobs\ProcessWebhookPayload;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Queue;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function validWhatsAppPayload(): array
{
    return [
        'object' => 'whatsapp_business_account',
        'entry' => [
            [
                'id' => '102290129340398',
                'changes' => [
                    [
                        'value' => [
                            'messaging_product' => 'whatsapp',
                            'metadata' => [
                                'display_phone_number' => '15550783881',
                                'phone_number_id' => '106540352242922',
                            ],
                            'contacts' => [
                                [
                                    'profile' => ['name' => 'Sheena Nelson'],
                                    'wa_id' => '16505551234',
                                ],
                            ],
                            'messages' => [
                                [
                                    'from' => '16505551234',
                                    'id' => 'wamid.HBgLMTY1MDM4Nzk0MzkVAgASGBQzQTRBNjU5OUFFRTAzODEwMTQ0RgA=',
                                    'timestamp' => '1749416383',
                                    'type' => 'text',
                                    'text' => [
                                        'body' => 'Does it come in another color?',
                                    ],
                                ],
                            ],
                        ],
                        'field' => 'messages',
                    ],
                ],
            ],
        ],
    ];
}

test('dispatches job and returns 200 when webhook request is valid', function () {
    Queue::fake();

    $payload = validWhatsAppPayload();
    $rawBody = json_encode($payload);

    $response = $this->call(
        method: 'POST',
        uri: '/api/whatsapp/webhook',
        server: [
            'CONTENT_TYPE' => 'application/json',
        ],
        content: $rawBody
    );

    $response->assertStatus(200)
        ->assertJson(['status' => 'EVENT_RECEIVED']);

    Queue::assertPushed(ProcessWebhookPayload::class, function ($job) use ($payload) {
        return $job->payload === $payload;
    });
});

test('rejects webhook request when payload structure is invalid', function () {
    $payload = ['object' => 'invalid_object_type'];
    $rawBody = json_encode($payload);
    $signature = 'sha256=' . hash_hmac('sha256', $rawBody, 'secret123');

    $response = $this->call(
        method: 'POST',
        uri: '/api/whatsapp/webhook',
        server: [
            'HTTP_X_HUB_SIGNATURE_256' => $signature,
            'CONTENT_TYPE' => 'application/json',
        ],
        content: $rawBody
    );

    $response->assertStatus(422)
        ->assertJson(['error' => 'Invalid payload structure']);
});