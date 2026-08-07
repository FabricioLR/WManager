<?php

use App\Http\Controllers\Api\WhatsAppWebhookController;
use Illuminate\Support\Facades\Route;

Route::post('/whatsapp/webhook', [WhatsAppWebhookController::class, 'handle']);