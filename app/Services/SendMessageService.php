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
        $now = now();
        
        $contact = Contact::firstOrCreate(
            ['wa_id' => $phoneNumber],
            [
                'name' => $phoneNumber,
                'phone_number' => $phoneNumber
            ]
        );

        $tempWamid = 'outbound_temp_' . Str::uuid();

        $message = Message::create([
            'contact_id' => $contact->id,
            'wamid'      => $tempWamid,
            'direction'  => 'outbound',
            'type'       => 'text',
            'body'       => $body,
            'timestamp'  => $now,
            'status'     => 'pending',
        ]);

        ProcessSentMessage::dispatch($message);

        return $message;
    }
}