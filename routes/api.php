<?php

use App\Http\Controllers\Api\WhatsAppWebhookController;
use App\Http\Middleware\ValidateWhatsAppWebhook;
use Illuminate\Support\Facades\Route;

Route::post('/whatsapp/webhook', [WhatsAppWebhookController::class, 'handle'])->middleware(ValidateWhatsAppWebhook::class);;