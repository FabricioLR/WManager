<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckApiSecretToken
{
    public function handle(Request $request, Closure $next): Response
    {
        $secretToken = config('services.api.secret_token', env('API_SECRET_TOKEN', ''));
        $providedToken = $request->header('X-Api-Secret');

        if (empty($secretToken) || $providedToken !== $secretToken) {
            return response()->json(['error' => 'Unauthorized. Invalid API Secret.'], 401);
        }

        return $next($request);
    }
}
