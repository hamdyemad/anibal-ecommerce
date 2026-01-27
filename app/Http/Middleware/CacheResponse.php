<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class CacheResponse
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  int  $ttl  Time to live in seconds (default: 60)
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $ttl = 120)
    {
        // Only cache GET requests
        if (!$request->isMethod('get')) {
            return $next($request);
        }

        // Generate cache key
        $key = $this->key($request);

        // Return cached response if exists
        if (Cache::has($key)) {
            $cachedData = Cache::get($key);
            
            return response()->json($cachedData, 200, [
                'X-Cache' => 'HIT',
                'X-Cache-Key' => $key,
            ]);
        }

        // Process request
        $response = $next($request);

        // Cache successful JSON responses
        if ($response->isSuccessful() && $response instanceof \Illuminate\Http\JsonResponse) {
            $data = $response->getData(true);
            
            Cache::put(
                $key,
                $data,
                now()->addSeconds((int) $ttl)
            );
            
            // Add cache miss header
            $response->headers->set('X-Cache', 'MISS');
            $response->headers->set('X-Cache-Key', $key);
        }

        return $response;
    }

    /**
     * Generate cache key from request
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string
     */
    protected function key(Request $request): string
    {
        // Include query parameters and user ID (if authenticated) in cache key
        $userId = auth()->check() ? auth()->id() : 'guest';
        $url = $request->fullUrl();
        
        return 'cache:response:' . sha1($userId . ':' . $url);
    }
}
