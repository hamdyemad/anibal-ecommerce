<?php

namespace Modules\Customer\app\Actions;

use Modules\Customer\app\Models\CustomerOtp;

class ValidateOtpAction
{
    /**
     * Validate OTP for given email and type
     */
    public function execute(string $email, string $otp, string $type): bool
    {
        $record = CustomerOtp::where('type', $type)
            ->where('email', $email)
            ->where('otp', $otp)
            ->where('expires_at', '>', now())
            ->whereNull('verified_at')
            ->first();

        if (!$record) {
            return false;
        }

        $record->markAsVerified();
        return true;
    }
}
