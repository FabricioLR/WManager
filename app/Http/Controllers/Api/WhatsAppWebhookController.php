<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Jobs\ProcessWebhookPayload;
use Illuminate\Http\Request;

class WhatsAppWebhookController extends Controller
{
    public function handle(Request $request)
    {
        $payload = $request->all();

        ProcessWebhookPayload::dispatch($payload);
        
        return response()->json(['status' => 'EVENT_RECEIVED'], 200);
    }
}
