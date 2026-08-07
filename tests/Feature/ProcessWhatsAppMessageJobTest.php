<?php

use App\Jobs\ProcessWhatsAppMessage;
use App\Models\Contact;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('stores contact and text message in database', function () {
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

    (new ProcessWhatsAppMessage($payload))->handle();

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

test('updates contact and ignores duplicate messages', function () {
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

    (new ProcessWhatsAppMessage($payload))->handle();
    (new ProcessWhatsAppMessage($payload))->handle();

    expect($contact->fresh()->name)->toBe('Updated Name');

    $this->assertDatabaseCount('messages', 1);
});