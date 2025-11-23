<?php

namespace Modules\Customer\app\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class ResetPasswordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => 'required|email|exists:customers,email',
            'reset_token' => 'required|string',
            'new_password' => 'required|string|min:8|confirmed',
            'fcm_token' => 'nullable|string',
            'device_id' => 'nullable|string|uuid',
        ];
    }
}
