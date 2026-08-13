<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class CheckApiSecretToken
{
    public function handle(Request $request, Closure $next): Response
    {
        $providedToken = $request->header('X-Api-Secret') ?? $request->query('api_token');

        if (empty($providedToken)) {
            return response()->json([
                'error' => 'Unauthorized. Missing API Secret Token.'
            ], 401);
        }

        $user = User::where('api_token', $providedToken)->first();

        if (!$user) {
            return response()->json([
                'error' => 'Unauthorized. Invalid API Secret Token.'
            ], 401);
        }

        Log::info("API Authentication Success for user ID [{$user->id}] ({$user->email})", [
            'user_id'  => $user->id,
            'email'    => $user->email,
            'endpoint' => $request->method() . ' ' . $request->path(),
            'ip'       => $request->ip(),
        ]);

        return $next($request);
    }
}