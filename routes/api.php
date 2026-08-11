<?php

use App\Http\Controllers\Api\WhatsAppTemplateController;
use App\Http\Controllers\Api\WhatsAppWebhookController;
use App\Http\Controllers\Api\SendMessageController;
use App\Http\Middleware\ValidateWhatsAppWebhook;
use App\Http\Middleware\CheckApiSecretToken;
use Illuminate\Support\Facades\Route;

Route::post('/whatsapp/webhook', [WhatsAppWebhookController::class, 'handle'])->middleware(ValidateWhatsAppWebhook::class);

Route::post('/messages/send', [SendMessageController::class, 'send'])->middleware(CheckApiSecretToken::class);
Route::post('/messages/sendTemplate', [SendMessageController::class, 'sendTemplate'])->middleware(CheckApiSecretToken::class);

Route::get('/templates', [WhatsAppTemplateController::class, 'get'])->middleware(CheckApiSecretToken::class);
