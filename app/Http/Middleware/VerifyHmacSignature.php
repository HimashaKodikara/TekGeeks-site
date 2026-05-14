<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifyHmacSignature
{
    public function handle(Request $request, Closure $next): Response
    {
        $apiKey = $request->header('X-API-KEY');
        $timestamp = $request->header('X-TIMESTAMP');
        $signature = $request->header('X-SIGNATURE');

        $expectedApiKey = config('services.backend_support_api.api_key');
        $secret = config('services.backend_support_api.api_secret');

        if (!$apiKey || !$timestamp || !$signature) {
            return response()->json([
                'status' => 'error',
                'message' => 'Missing authentication headers.',
            ], 401);
        }

        if (!hash_equals((string) $expectedApiKey, (string) $apiKey)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized.',
            ], 401);
        }

        if (!ctype_digit((string) $timestamp)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid timestamp.',
            ], 401);
        }

        if (abs(time() - (int) $timestamp) > 60) {
            return response()->json([
                'status' => 'error',
                'message' => 'Request expired.',
            ], 401);
        }

        $body = $request->getContent();

        $expectedSignature = hash_hmac(
            'sha256',
            $body . $timestamp,
            $secret
        );

        if (!hash_equals($expectedSignature, $signature)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid signature.',
            ], 401);
        }

        return $next($request);
    }
}
