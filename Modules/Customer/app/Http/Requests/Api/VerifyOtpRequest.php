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
}
