<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\SendMessageRequest;
use App\Services\SendMessageService;
use Illuminate\Http\JsonResponse;

class SendMessageController extends Controller
{
    public function __construct(
        protected SendMessageService $sendMessageService
    ){}
    public function send(SendMessageRequest $request): JsonResponse
    {
        $message = $this->sendMessageService->send(
            phoneNumber: $request->validated('phone_number'),
            body: $request->validated('message')
        );

        return response()->json([
            'status'  => 'queued',
            'message' => 'Message queued for sending.',
            'data'    => [
                'message_id' => $message->id,
                'contact_id' => $message->contact_id,
                'status'     => $message->status,
            ],
        ], 202);
    }
}