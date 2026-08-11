<?php

namespace App\Services;

use App\Jobs\ProcessSentMessage;
use App\Models\Contact;
use App\Models\Message;
use Illuminate\Support\Str;

class SendMessageService
{
    public function send(string $phoneNumber, string $body): Message
    {
        return $this->createAndDispatchMessage(
            phoneNumber: $phoneNumber,
            type: 'text',
            body: $body
        );
    }

    public function sendTemplate(
        string $phoneNumber, 
        string $templateName, 
        string $languageCode = 'en_US', 
        array $components = [],
        ?string $fallbackBody = null
    ): Message {
        $payload = [
            'name' => $templateName,
            'language' => ['code' => $languageCode],
            'components' => $components,
        ];

        return $this->createAndDispatchMessage(
            phoneNumber: $phoneNumber,
            type: 'template',
            body: $fallbackBody ?? "Template: {$templateName}",
            payload: $payload
        );
    }

    private function createAndDispatchMessage(
        string $phoneNumber, 
        string $type, 
        string $body, 
        ?array $payload = null
    ): Message {
        $contact = Contact::firstOrCreate(
            ['wa_id' => $phoneNumber],
            [
                'name' => $phoneNumber,
                'phone_number' => $phoneNumber
            ]
        );

        $message = Message::create([
            'contact_id' => $contact->id,
            'wamid'      => 'outbound_temp_' . Str::uuid(),
            'direction'  => 'outbound',
            'type'       => $type,
            'body'       => $body,
            'payload'    => $payload,
            'timestamp'  => now(),
            'status'     => 'pending',
        ]);

        ProcessSentMessage::dispatch($message);

        return $message;
    }
}