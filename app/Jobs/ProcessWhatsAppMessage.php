<?php

namespace App\Jobs;

use App\Models\Contact;
use App\Models\Message;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class ProcessWhatsAppMessage implements ShouldQueue
{
    use Queueable;

    public function __construct(public array $payload) {}

    public function handle(): void
    {
        Log::info('Processing WhatsApp message', ['payload' => $this->payload]);
        $value = $this->payload['entry'][0]['changes'][0]['value'] ?? [];
        $contactData = $value['contacts'][0] ?? null;
        $messageData = $value['messages'][0] ?? null;

        if (!$messageData) {
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
                'last_message_at' => $msgTimestamp,
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
        Log::info('WhatsApp message processed', ['contact_id' => $contact->id]);
    }
}