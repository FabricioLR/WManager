<?php

namespace App\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TemplateService
{
    public function getTemplates(?string $limit = null, ?string $after = null): array
    {
        $accountId   = Config::get('services.whatsapp.account_id', env('WHATSAPP_ACCOUNT_ID', ''));
        $accessToken = Config::get('services.whatsapp.access_token', env('WHATSAPP_ACCESS_TOKEN', ''));
        $apiVersion  = Config::get('services.whatsapp.api_version', env('WHATSAPP_API_VERSION', 'v26.0'));

        if (!$accountId || !$accessToken) {
            Log::error("WhatsApp API credentials missing for fetching templates.");
            return [
                'data' => [],
                'error' => 'WhatsApp API credentials are not configured.'
            ];
        }

        $url = "https://graph.facebook.com/{$apiVersion}/{$accountId}/message_templates";

        $queryParams = array_filter([
            'limit' => $limit,
            'after' => $after,
        ]);

        try {
            $response = Http::timeout(15)
                ->retry(3, 100, function ($exception) {
                    return $exception instanceof ConnectionException || 
                           ($exception instanceof RequestException && $exception->response?->serverError());
                }, throw: false)
                ->withToken($accessToken)
                ->get($url, $queryParams);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error("Failed to fetch WhatsApp templates from Meta API.", [
                'status' => $response->status(),
                'error'  => $response->json(),
            ]);

            return [
                'data'  => [],
                'error' => 'Unable to fetch message templates from Meta.',
            ];
        } catch (\Exception $e) {
            Log::error("Exception occurred while fetching WhatsApp templates: " . $e->getMessage());

            return [
                'data'  => [],
                'error' => 'An error occurred while connecting to WhatsApp API.',
            ];
        }
    }
}