<?php

namespace App\Jobs;

use App\Models\Message;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ProcessSentMessage implements ShouldQueue
{
    use Queueable;

    public function __construct(public Message $message) {}

    public function handle(): void
    {
        $this->message->load('contact');
        $contact = $this->message->contact;

        $phoneNumberId = config('services.whatsapp.phone_number_id', env("WHATSAPP_PHONE_ID"));
        $accessToken   = config('services.whatsapp.access_token', env("WHATSAPP_ACCESS_TOKEN"));
        $apiVersion    = config('services.whatsapp.api_version', env("WHATSAPP_API_VERSION", 'v26.0'));

        if (!$phoneNumberId || !$accessToken) {
            Log::error("WhatsApp API credentials missing.");
            return;
        }

        $isWithin24Hours = $contact->last_message_from_contact_at && 
            $contact->last_message_from_contact_at->greaterThanOrEqualTo(now()->subHours(24));

        if ($this->message->type === 'text' && !$isWithin24Hours) {
            $this->message->update(['status' => 'failed']);
            Log::warning("Cannot send text message outside 24-hour window.", ['message_id' => $this->message->id]);
            return;
        }

        $payload = [
            'messaging_product' => 'whatsapp',
            'recipient_type'    => 'individual',
            'to'                => $contact->phone_number,
            'type'              => $this->message->type,
        ];

        if ($this->message->type === 'template') {
            $payload['template'] = $this->message->payload;
        } else {
            $payload['text'] = [
                'preview_url' => false,
                'body'        => $this->message->body,
            ];
        }
        
        $response = Http::timeout(15)
            ->retry(3, 100, function ($exception) {
                return $exception instanceof ConnectionException || 
                       ($exception instanceof RequestException && $exception->response?->serverError());
            }, throw: false)
            ->withToken($accessToken)
            ->post("https://graph.facebook.com/{$apiVersion}/{$phoneNumberId}/messages", $payload);

        if ($response->successful()) {
            $officialWamid = $response->json('messages.0.id');

            $this->message->update([
                'wamid'  => $officialWamid ?? $this->message->wamid,
                'status' => 'sending',
            ]);
        } else {
            $this->message->update(['status' => 'failed']);

            Log::error("Failed to send WhatsApp message via Meta API.", [
                'message_id' => $this->message->id,
                'error'      => $response->json(),
            ]);
        }
    }
}