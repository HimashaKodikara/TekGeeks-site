<?php

namespace App\Http\Middleware;

use Closure;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

class VerifySyncTokenMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, Closure $next)
    {
        $token = $request->bearerToken();
        $secret = config('services.backend_api.secret');

        Log::error("secret Sync Error: No token provided" .$secret);

        if (!$token) {
            Log::error("JWT Sync Error: No token provided");
            return response()->json(['error' => 'No token provided'], 401);
        }

        try {

            JWT::$leeway = 60;

            $decoded = JWT::decode($token, new Key($secret, 'HS256'));

            $redisKey = "used_jti:" . $decoded->jti;
            
            if (Redis::exists($redisKey)) {
                Log::warning("Replay attack blocked: JTI {$decoded->jti}");
                return response()->json(['error' => 'Token already used'], 401);
            }

            $ttl = $decoded->exp - now()->timestamp;
            if ($ttl > 0) {
                Redis::setex($redisKey, $ttl, 'used');
            }

        } catch (\Firebase\JWT\SignatureInvalidException $e) {
            Log::error("JWT Sync Error: Signature Invalid. Check if BACKEND_JWT_SECRET is identical on both servers.");
            return response()->json(['error' => 'Signature verification failed'], 401);
        } catch (\Firebase\JWT\BeforeValidException $e) {
            Log::error("JWT Sync Error: Token not yet valid. Server clocks are out of sync.");
            return response()->json(['error' => 'Token not yet valid'], 401);
        } catch (\Firebase\JWT\ExpiredException $e) {
            Log::error("JWT Sync Error: Token has expired.");
            return response()->json(['error' => 'Token expired'], 401);
        } catch (\Exception $e) {
            Log::error("JWT Sync Error: " . $e->getMessage());
            return response()->json(['error' => 'Authentication failed'], 401);
        }

        return $next($request);
    }
}
