<?php

use App\Jobs\ProcessWebhookPayload;
use App\Models\Contact;
use App\Models\Message;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('stores contact and text message in database when type of payload is message', function () {
    $payload = [
        'entry' => [
            [
                'changes' => [
                    [
                        'value' => [
                            'contacts' => [
                                [
                                    'profile' => ['name' => 'John Doe'],
                                    'wa_id' => '5561999999999',
                                ],
                            ],
                            'messages' => [
                                [
                                    'from' => '5561999999999',
                                    'id' => 'wamid.TEST_ID_12345',
                                    'timestamp' => '1700000000',
                                    'type' => 'text',
                                    'text' => [
                                        'body' => 'Hello from Pest test!',
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ];

    (new ProcessWebhookPayload($payload))->handle();

    $this->assertDatabaseHas('contacts', [
        'wa_id' => '5561999999999',
        'name' => 'John Doe',
        'phone_number' => '5561999999999',
    ]);

    $this->assertDatabaseHas('messages', [
        'wamid' => 'wamid.TEST_ID_12345',
        'direction' => 'inbound',
        'type' => 'text',
        'body' => 'Hello from Pest test!',
        'status' => 'received',
    ]);
});

test('updates contact and ignores duplicate messages when type of payload is message', function () {
    $contact = Contact::create([
        'wa_id' => '5561999999999',
        'name' => 'Old Name',
        'phone_number' => '5561999999999',
    ]);

    $payload = [
        'entry' => [
            [
                'changes' => [
                    [
                        'value' => [
                            'contacts' => [
                                [
                                    'profile' => ['name' => 'Updated Name'],
                                    'wa_id' => '5561999999999',
                                ],
                            ],
                            'messages' => [
                                [
                                    'from' => '5561999999999',
                                    'id' => 'wamid.DUPLICATE_ID',
                                    'timestamp' => '1700000000',
                                    'type' => 'text',
                                    'text' => ['body' => 'First delivery'],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ];

    (new ProcessWebhookPayload($payload))->handle();
    (new ProcessWebhookPayload($payload))->handle();

    expect($contact->fresh()->name)->toBe('Updated Name');

    $this->assertDatabaseCount('messages', 1);
});

test('updates message status when type of payload is statuses', function () {
    $contact = Contact::create([
        'wa_id' => '5561999999999',
        'name' => 'Old Name',
        'phone_number' => '5561999999999',
    ]);

    $message  = Message::create([
        'contact_id' => $contact->id,
        'wamid' => 'wamid.TEST_ID_12345',
        'direction' => 'outbound',
        'type' => 'text',
        'body' => 'Hello from Pest test!',
        'timestamp'  => '1700000000',
        'status' => 'sending',
    ]);

    $payload = [
        'entry' => [
            [
                'changes' => [
                    [
                        'value' => [
                            'contacts' => [
                                [
                                    'profile' => ['name' => 'John Doe'],
                                    'wa_id' => '5561999999999',
                                ],
                            ],
                            'statuses' => [
                                [
                                    'recipient_id' => '5561999999999',
                                    'id' => 'wamid.TEST_ID_12345',
                                    'status' => 'sent',
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ];

    (new ProcessWebhookPayload($payload))->handle();

    $this->assertDatabaseHas('messages', [
        'wamid' => 'wamid.TEST_ID_12345',
        'direction' => 'outbound',
        'type' => 'text',
        'body' => 'Hello from Pest test!',
        'status' => 'sent',
    ]);
});