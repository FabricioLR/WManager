<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckApiSecretToken
{
    public function handle(Request $request, Closure $next): Response
    {
        $secretToken = config('admin.api_token', env('ADMIN_API_TOKEN', '123456'));
        $providedToken = $request->header('X-Api-Secret');

        if (empty($secretToken) || $providedToken !== $secretToken) {
            return response()->json(['error' => 'Unauthorized. Invalid API Secret.'], 401);
        }

        return $next($request);
    }
}
