<?php

namespace App\Jobs;

use App\Models\Contact;
use App\Models\Message;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class ProcessWebhookPayload implements ShouldQueue
{
    use Queueable;

    public function __construct(public array $payload) {}

    public function handle(): void
    {
        Log::info('Processing WhatsApp message', ['payload' => $this->payload]);

        $value = $this->payload['entry'][0]['changes'][0]['value'] ?? [];
        if (isset($value['messages'])) {
            $this->processMessagePayload();
        } else if (isset($value['statuses'])) {
            $this->processStatusesPayload();
        } else {
            Log::warning('Process handling not implemented for this type of payload', [
                'payload' => $this->payload
            ]);
            return;
        }

        Log::info('WhatsApp message processed');
    }

    public function processMessagePayload(){
        $value = $this->payload['entry'][0]['changes'][0]['value'] ?? [];
        $contactData = $value['contacts'][0] ?? null;
        $messageData = $value['messages'][0] ?? null;

        if (!$messageData) {
            Log::warning('No message data found in the payload', [
                'payload' => $this->payload
            ]); 
            return;
        }

        $waId = $contactData['wa_id'] ?? $messageData['from'];
        $name = $contactData['profile']['name'] ?? $waId;
        $msgTimestamp = Carbon::createFromTimestamp($messageData['timestamp']);

        $contact = Contact::updateOrCreate(
            ['wa_id' => $waId],
            [
                'name' => $name,
                'phone_number' => $messageData['from'],
                'last_message_from_contact_at' => $msgTimestamp
            ]
        );

        $body = null;
        if ($messageData['type'] === 'text') {
            $body = $messageData['text']['body'] ?? '';
        } else {
            $body = '[' . ucfirst($messageData['type']) . ' Message]';
        }

        Message::firstOrCreate(
            ['wamid' => $messageData['id']],
            [
                'contact_id' => $contact->id,
                'direction' => 'inbound',
                'type' => $messageData['type'],
                'body' => $body,
                'timestamp' => $msgTimestamp,
                'status' => 'received',
            ]
        );
    }

    public function processStatusesPayload() {
        $value = $this->payload['entry'][0]['changes'][0]['value'] ?? [];
        $contactData = $value['contacts'][0] ?? null;
        $statusesData = $value['statuses'][0] ?? null;

        if (!$statusesData) {
            Log::warning('No statuses data found in the payload', [
                'payload' => $this->payload
            ]);
            return;
        }

        $waId = $contactData['wa_id'] ?? $statusesData['recipient_id'];
        $name = $contactData['profile']['name'] ?? $waId;

        Contact::updateOrCreate(
            ['wa_id' => $waId],
            [
                'name' => $name,
                'phone_number' => $statusesData['recipient_id']
            ]
        );

        Message::where('wamid', $statusesData['id'])->update(
            [
                'status' => $statusesData['status'],
            ]
        );
    }
}