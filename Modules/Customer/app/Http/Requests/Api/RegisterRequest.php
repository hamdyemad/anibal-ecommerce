<?php

namespace Modules\Customer\app\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:customers,email',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'nullable|string|unique:customers,phone',
            'lang' => 'nullable|string|in:en,ar',
        ];
    }

    public function validated($key = null, $default = null)
    {
        return parent::validated($key, $default);
    }
}
