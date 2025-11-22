<?php

namespace Modules\Customer\app\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class VerifyOtpRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => 'required|email|exists:customers,email',
            'otp' => 'required|string|size:6|regex:/^\d{6}$/',
            'fcm_token' => 'sometimes|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => 'Email is required',
            'email.email' => 'Email must be valid',
            'email.exists' => 'Email not found',
            'otp.required' => 'OTP is required',
            'otp.size' => 'OTP must be exactly 6 digits',
            'otp.regex' => 'OTP must contain only numbers',
        ];
    }
}
