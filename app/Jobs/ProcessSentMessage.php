<?php

namespace App\Jobs;

use App\Models\Message;
use App\Models\Contact;
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
        $phoneNumber = $contact->phone_number;

        $phoneNumberId = config('services.whatsapp.phone_number_id', env("WHATSAPP_PHONE_ID"));
        $accessToken = config('services.whatsapp.access_token', env("WHATSAPP_ACCESS_TOKEN"));
        $apiVersion = config('services.whatsapp.api_version', env("WHATSAPP_API_VERSION", 'v26.0'));

        if (!$phoneNumberId || !$accessToken) {
            Log::error("WhatsApp API credentials are not set.");
            return;
        }

        $isWithin24Hours = $contact->last_message_from_contact_at && 
            $contact->last_message_from_contact_at->greaterThanOrEqualTo(now()->subHours(24));

        if ($isWithin24Hours) {
            $payload = [
                'messaging_product' => 'whatsapp',
                'recipient_type'    => 'individual',
                'to'                => $phoneNumber,
                'type'              => 'text',
                'text'              => [
                    'preview_url' => false,
                    'body'        => $this->message->body,
                ],
            ];
        } else {
            $payload = [
                'messaging_product' => 'whatsapp',
                'recipient_type'    => 'individual',
                'to'                => $phoneNumber,
                'type'              => 'template',
                'template'          => [
                    'name'     => 'hello_world',
                    'language' => [
                        'code' => 'en_US',
                    ],
                ],
            ];
        }

        $response = Http::timeout(15)
            ->retry(3, 100, when: function ($exception) {
                return $exception instanceof ConnectionException || 
                        ($exception instanceof RequestException && $exception->response?->serverError());
            }, throw: false)->withToken($accessToken)
            ->post("https://graph.facebook.com/{$apiVersion}/{$phoneNumberId}/messages", $payload);

        if ($response->successful()) {
            $responseData = $response->json();
            $officialWamid = $responseData['messages'][0]['id'] ?? null;

            $this->message->update([
                'wamid'  => $officialWamid ?? $this->message->wamid,
                'status' => 'sending',
            ]);

            Log::info("WhatsApp outbound message sent successfully.", [
                'message_id'       => $this->message->id,
                'wamid'            => $officialWamid,
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
