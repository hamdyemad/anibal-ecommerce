<?php

namespace Modules\Customer\app\Listeners;

use Illuminate\Support\Facades\Log;
use Modules\Customer\app\Events\OtpCreated;
use Modules\Customer\app\Mail\OtpMail;
use Modules\Customer\app\Models\Customer;
use Illuminate\Support\Facades\Mail;
use Modules\Customer\app\Models\CustomerOtp;
use Modules\Customer\app\Services\Api\CustomerAuthService;

class SendOtpEmail
{
    public function handle(OtpCreated $event): void
    {
        try {
            $customer = $event->custoemr;
            app(CustomerAuthService::class)->saveOtp(
                email: $customer->email,
                otp: $event->otp,
                type: $event->type,
                expiresInMinutes: $event->expiresInMinutes,
            );

            $language = $customer?->language ?? 'en';

            Mail::to($customer->email)->send(new OtpMail(
                email: $customer->email,
                otp: $event->otp,
                type: $event->type,
                expiresInMinutes: $event->expiresInMinutes,
                language: $language,
            ));
        } catch (\Exception $e) {
            Log::error('Failed to send OTP email: ' . $e->getMessage());
            throw $e;
        }
    }
}
