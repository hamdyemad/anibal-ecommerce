<?php

namespace Modules\Customer\app\Repositories\Api;

use Modules\Customer\app\Actions\CreateCustomerAction;
use Modules\Customer\app\Interfaces\Api\CustomerApiRepositoryInterface;
use Modules\Customer\app\Models\Customer;
use Modules\Customer\app\Models\CustomerOtp;
use Modules\Customer\app\Models\CustomerPasswordResetToken;
use Modules\Customer\app\Models\CustomerAddress;
use Modules\Customer\app\Events\OtpCreated;
use Illuminate\Support\Str;
use Laravel\Sanctum\PersonalAccessToken;
use Modules\Customer\Actions\UpdateCustomerInfoAction;
use Modules\Customer\Actions\UpdateCustomerPasswordAction;

class CustomerApiRepository implements CustomerApiRepositoryInterface
{
    public function __construct(
        protected CreateCustomerAction $createCustomerAction,
        protected UpdateCustomerInfoAction $updateCustomerInfoAction,
        protected UpdateCustomerPasswordAction $updateCustomerPasswordAction,
    ) {}


    public function getByEmail(string $email): ?Customer
    {
        return Customer::where('email', $email)->first();
    }

    public function create(array $data): Customer
    {
        return $this->createCustomerAction->execute($data);
    }

    public function createOtp(string $email, string $otp, string $type, int $expiresInMinutes = 10): CustomerOtp
    {
        return CustomerOtp::create([
            'email' => $email,
            'otp' => $otp,
            'type' => $type,
            'expires_at' => now()->addMinutes($expiresInMinutes),
        ]);
    }

    public function verifyEmail(Customer $customer): void
    {
        $customer->update(['email_verified_at' => now()]);
    }

    public function getPasswordResetToken(string $email, string $token): ?CustomerPasswordResetToken
    {
        return CustomerPasswordResetToken::where('email', $email)
            ->where('token', $token)
            ->where('expires_at', '>', now())
            ->first();
    }

    public function createPasswordResetToken(string $email, string $token): CustomerPasswordResetToken
    {
        return CustomerPasswordResetToken::updateOrCreate(
            ['email' => $email],
            [
                'token' => $token,
                'created_at' => now(),
                'expires_at' => now()->addHours(1),
            ]
        );
    }

    public function deletePasswordResetToken(string $email): void
    {
        CustomerPasswordResetToken::where('email', $email)->delete();
    }


    public function createTokens(Customer $customer, ?string $fcmToken = null, ?string $deviceId = null): array
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

    public function getByToken(string $token): ?Customer
    {
        $parts = explode('|', $token);
        if (count($parts) !== 2) {
            return null;
        }

        $tokenHash = hash('sha256', $parts[1]);

        $customer = Customer::whereHas('tokens', function ($query) use ($tokenHash) {
            $query->where('token', $tokenHash)->where('expires_at', '>', now());
        })->first();

        if (!$customer) {
            return null;
        }

        return $customer;
    }

    public function revokeTokens(Customer $customer, ?string $deviceId = null): void
    {
        if (!empty($deviceId)) {
            $customer->tokens()
                ->whereIn('name', ['access_token', 'refresh_token'])
                ->where('device_id', $deviceId)
                ->delete();
        } else {
            $customer->tokens()
                ->whereIn('name', ['access_token', 'refresh_token'])
                ->delete();
        }
    }

    public function getProfile(Customer $customer): Customer
    {
        $customer->load(["addresses"]);

        return $customer;
    }

    public function updateInfo(Customer $customer, array $data): Customer
    {
        $customer = $this->updateCustomerInfoAction->execute($customer, $data);

        return $customer;
    }

    public function updatePassword(Customer $customer, string $newPassword): Customer
    {
        $customer = $this->updateCustomerPasswordAction->execute($customer, $newPassword);

        return $customer;
    }

    public function changeLanguage(Customer $customer, string $lang): Customer
    {
        $customer = $this->updateCustomerInfoAction->execute($customer, ['lang' => $lang]);

        return $customer;
    }
}

