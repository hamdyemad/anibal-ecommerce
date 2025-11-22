<?php

namespace Modules\Customer\app\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => 'required|email|exists:customers,email',
            'password' => 'required|string',
            'fcm_token' => 'nullable|string',
            'device_id' => 'nullable|string|uuid',
        ];
    }
}
