<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;

class ValidateWhatsAppWebhook
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!$this->hasValidStructure($request)) {
            Log::warning('WhatsApp Webhook received invalid payload structure.', [
                'payload' => $request->all()
            ]);
            return response()->json(['error' => 'Invalid payload structure'], 422);
        }

        return $next($request);
    }

    private function hasValidStructure(Request $request): bool
    {
        $payload = $request->all();

        if (($payload['object'] ?? null) !== 'whatsapp_business_account') {
            return false;
        }

        if (empty($payload['entry']) || !is_array($payload['entry'])) {
            return false;
        }

        $changes = $payload['entry'][0]['changes'] ?? null;
        if (empty($changes) || !is_array($changes)) {
            return false;
        }

        $value = $changes[0]['value'] ?? null;
        if (!$value || !is_array($value)) {
            return false;
        }

        return isset($value['messages']) || isset($value['statuses']);
    }
}
