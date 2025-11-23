<?php

namespace Modules\Customer\app\Actions;

use Modules\Customer\app\Models\Customer;
use Modules\Customer\app\Models\CustomerAccessToken;
use Illuminate\Support\Str;

class CreateTokensAction
{
    /**
     * Create access and refresh tokens for customer
     */
    public function execute(Customer $customer, ?string $fcmToken = null, ?string $deviceId = null): array
    {
        // Generate device ID if not provided
        $deviceId = $deviceId ?? Str::uuid()->toString();

        // Create access token (7 days)
        $accessToken = $customer->createToken(
            'access_token',
            ['*'],
            now()->addDays(7)
        );
        $accessToken->accessToken->update(['device_id' => $deviceId]);

        // Create refresh token (30 days)
        $refreshToken = $customer->createToken(
            'refresh_token',
            ['*'],
            now()->addDays(30)
        );
        $refreshToken->accessToken->update(['device_id' => $deviceId]);

        // Store FCM token if provided
        if ($fcmToken) {
            $customer->fcmTokens()->updateOrCreate(
                ['fcm_token' => $fcmToken],
                [
                    'device_name' => request()->header('User-Agent'),
                ]
            );
        }

        return [
            'access_token' => [
                'token' => $accessToken->plainTextToken,
                'expires_at' => $accessToken->accessToken->expires_at,
            ],
            'refresh_token' => [
                'token' => $refreshToken->plainTextToken,
                'expires_at' => $refreshToken->accessToken->expires_at,
            ],
            'fcm_token' => $fcmToken,
            'device_id' => $deviceId,
        ];
    }
}
