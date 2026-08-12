<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\TemplateService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WhatsAppTemplateController extends Controller
{
    public function __construct(
        protected TemplateService $templateService
    ) {}

    public function get(Request $request): JsonResponse
    {
        $templates = $this->templateService->getTemplates(
            limit: $request->query('limit'),
            after: $request->query('after')
        );

        $status = isset($templates['error']) ? 500 : 200;

        return response()->json($templates, $status);
    }
}