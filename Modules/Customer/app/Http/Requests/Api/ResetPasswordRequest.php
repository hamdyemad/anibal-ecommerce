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

    public function messages(): array
    {
        return [
            'email.required' => 'Email is required',
            'email.email' => 'Email must be valid',
            'email.exists' => 'Email not found',
            'reset_token.required' => 'Reset token is required',
            'new_password.required' => 'Password is required',
            'new_password.min' => 'Password must be at least 8 characters',
            'new_password.confirmed' => 'Password confirmation does not match',
        ];
    }
}
