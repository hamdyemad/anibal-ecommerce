<?php

namespace App\Http\Middleware\Api;

use App\Models\ActivityLog;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class ApiActivityLogMiddleware
{
    /**
     * Sensitive fields to mask in logs
     */
    protected array $sensitiveFields = [
        'password',
        'password_confirmation',
        'current_password',
        'new_password',
        'token',
        'api_token',
        'access_token',
        'refresh_token',
        'secret',
        'credit_card',
        'card_number',
        'cvv',
        'pin',
        'otp',
        'verification_code',
    ];

    /**
     * Routes to exclude from logging
     */
    protected array $excludedRoutes = [
        'api/health',
        'api/ping',
    ];

    /**
     * Store request start time
     */
    protected float $startTime;

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $this->startTime = microtime(true);

        return $next($request);
    }

    /**
     * Handle tasks after the response has been sent to the browser.
     * This runs AFTER all route middleware (including auth:sanctum) have executed.
     */
    public function terminate(Request $request, Response $response): void
    {
        // Log asynchronously after response is sent
        // Using register_shutdown_function to avoid queue serialization issues
        $startTime = $this->startTime ?? microtime(true);
        $self = $this;
        
        register_shutdown_function(function() use ($request, $response, $startTime, $self) {
            $self->logApiActivity($request, $response, $startTime);
        });
    }

    /**
     * Log API activity
     */
    public function logApiActivity(Request $request, Response $response, float $startTime): void
    {
        try {
            // Skip excluded routes
            if ($this->shouldSkip($request)) {
                return;
            }

            $duration = round((microtime(true) - $startTime) * 1000, 2);
            
            // Get authenticated user - try multiple methods
            $user = $request->user() 
                ?? $request->user('sanctum') 
                ?? auth('sanctum')->user() 
                ?? auth()->user();

            // Determine action based on HTTP method
            $action = $this->getActionFromMethod($request->method());

            // Get request and response data
            $requestData = $this->maskSensitiveData($request->all());
            $responseData = $this->getResponseData($response);

            $userType = $this->getUserType($user);
            $actorInfo = $this->getActorInfo($user, $userType);

            // Determine customer_id based on user type
            $customerId = null;
            if ($userType === 'customer' && $user) {
                $customerId = $user->id;
            }

            ActivityLog::create([
                'user_id' => $userType === 'admin' ? $user?->id : null,
                'customer_id' => $customerId,
                'action' => $action,
                'model' => 'ApiRequest',
                'model_id' => null,
                'description_key' => 'activity_log.api_request',
                'description_params' => [
                    'method' => $request->method(),
                    'endpoint' => $request->path(),
                    'status' => $response->getStatusCode(),
                ],
                'properties' => [
                    'actor' => $actorInfo,
                    'request' => [
                        'method' => $request->method(),
                        'url' => $request->fullUrl(),
                        'path' => $request->path(),
                        'query' => $this->maskSensitiveData($request->query()),
                        'body' => $requestData,
                        'headers' => $this->getRelevantHeaders($request),
                    ],
                    'response' => [
                        'status' => $response->getStatusCode(),
                        'data' => $responseData,
                    ],
                    'performance' => [
                        'duration_ms' => $duration,
                    ],
                ],
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                // country_id will be auto-filled by AutoStoreCountryId trait
            ]);
        } catch (\Exception $e) {
            Log::error('ApiActivityLogMiddleware error: ' . $e->getMessage(), [
                'path' => $request->path(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }

    /**
     * Check if route should be skipped
     */
    protected function shouldSkip(Request $request): bool
    {
        $path = $request->path();

        foreach ($this->excludedRoutes as $excluded) {
            if (str_starts_with($path, $excluded)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get action name from HTTP method
     */
    protected function getActionFromMethod(string $method): string
    {
        return match (strtoupper($method)) {
            'GET' => 'api_read',
            'POST' => 'api_create',
            'PUT', 'PATCH' => 'api_update',
            'DELETE' => 'api_delete',
            default => 'api_request',
        };
    }

    /**
     * Mask sensitive data in array
     */
    protected function maskSensitiveData(array $data): array
    {
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $data[$key] = $this->maskSensitiveData($value);
            } elseif ($this->isSensitiveField($key)) {
                $data[$key] = '***MASKED***';
            }
        }

        return $data;
    }

    /**
     * Check if field is sensitive
     */
    protected function isSensitiveField(string $field): bool
    {
        $field = strtolower($field);

        foreach ($this->sensitiveFields as $sensitive) {
            if (str_contains($field, $sensitive)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get relevant headers for logging
     */
    protected function getRelevantHeaders(Request $request): array
    {
        return [
            'accept' => $request->header('Accept'),
            'content-type' => $request->header('Content-Type'),
            'accept-language' => $request->header('Accept-Language'),
            'x-country-id' => $request->header('X-Country-Id'),
            'x-requested-with' => $request->header('X-Requested-With'),
        ];
    }

    /**
     * Get response data (truncated if too large)
     */
    protected function getResponseData(Response $response): ?array
    {
        try {
            $content = $response->getContent();
            
            // Skip if content is too large (> 10KB)
            if (strlen($content) > 10240) {
                return ['_truncated' => true, 'size' => strlen($content)];
            }

            $decoded = json_decode($content, true);
            
            if (json_last_error() === JSON_ERROR_NONE) {
                return $this->maskSensitiveData($decoded);
            }

            return null;
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Get user type for logging
     */
    protected function getUserType($user): ?string
    {
        if (!$user) {
            return 'guest';
        }

        $class = get_class($user);

        return match (true) {
            str_contains($class, 'Customer') => 'customer',
            str_contains($class, 'Vendor') => 'vendor',
            str_contains($class, 'User') => 'admin',
            default => 'unknown',
        };
    }

    /**
     * Get actor information for logging
     */
    protected function getActorInfo($user, string $userType): array
    {
        if (!$user) {
            return [
                'type' => 'guest',
                'id' => null,
                'name' => 'Guest',
                'email' => null,
            ];
        }

        return [
            'type' => $userType,
            'id' => $user->id,
            'name' => $user->name ?? null,
            'email' => $user->email ?? null,
            'phone' => $user->phone ?? null,
        ];
    }
}
