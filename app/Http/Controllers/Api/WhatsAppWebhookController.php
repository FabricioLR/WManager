<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Jobs\ProcessWhatsAppMessage;
use Illuminate\Http\Request;

class WhatsAppWebhookController extends Controller
{
    public function handle(Request $request)
    {
        $payload = $request->all();

        if (isset($payload['entry'][0]['changes'][0]['value']['messages'])) {
            ProcessWhatsAppMessage::dispatch($payload);
        }

        return response()->json(['status' => 'EVENT_RECEIVED'], 200);
    }
}
