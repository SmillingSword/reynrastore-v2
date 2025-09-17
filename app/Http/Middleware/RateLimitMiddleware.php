<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Symfony\Component\HttpFoundation\Response;

class RateLimitMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, string $key = 'global', int $maxAttempts = 60, int $decayMinutes = 1): Response
    {
        $limiterKey = $this->resolveRequestSignature($request, $key);

        if (RateLimiter::tooManyAttempts($limiterKey, $maxAttempts)) {
            $seconds = RateLimiter::availableIn($limiterKey);
            
            return response()->json([
                'success' => false,
                'message' => 'Too many requests. Please try again in ' . $seconds . ' seconds.',
                'retry_after' => $seconds
            ], 429);
        }

        RateLimiter::hit($limiterKey, $decayMinutes * 60);

        $response = $next($request);

        // Add rate limit headers
        $response->headers->set('X-RateLimit-Limit', $maxAttempts);
        $response->headers->set('X-RateLimit-Remaining', max(0, $maxAttempts - RateLimiter::attempts($limiterKey)));

        return $response;
    }

    /**
     * Resolve the rate limiter key for the request.
     */
    protected function resolveRequestSignature(Request $request, string $key): string
    {
        $signature = $request->ip();

        if ($request->user()) {
            $signature = 'user:' . $request->user()->id;
        }

        return 'rate_limit:' . $key . ':' . sha1($signature);
    }
}
