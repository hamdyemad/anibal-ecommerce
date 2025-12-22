<?php

namespace App\Http\Requests\UsersVendorsManagement;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Language;
use App\Models\User;

class UserVendorRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $userVendor = $this->resolveUserVendor();
        $isUpdate = !is_null($userVendor);
        
        $emailRule = 'required|email|max:255|unique:users,email';
        if ($isUpdate && $userVendor) {
            $emailRule = 'required|email|max:255|unique:users,email,' . $userVendor->id;
        }
        
        $passwordRule = 'required|string|min:8|confirmed';
        if ($isUpdate) {
            $passwordRule = 'nullable|string|min:8|confirmed';
        }
        
        return [
            'translations' => 'required|array|min:1',
            'translations.*.name' => 'required|string|max:255',
            'active' => 'nullable|boolean',
            'block' => 'nullable|boolean',
            'role_ids' => 'required|array|min:1',
            'role_ids.*' => 'required|exists:roles,id',
            'email' => $emailRule,
            'password' => $passwordRule,
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ];
    }

    /**
     * Resolve user from route
     */
    protected function resolveUserVendor(): ?User
    {
        $param = $this->route('users_vendor');
        if ($param instanceof User) {
            return $param;
        }

        return is_numeric($param)
            ? User::find($param)
            : null;
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'translations.*.name' => __('user_vendor.name'),
            'email' => __('user_vendor.email'),
            'password' => __('user_vendor.password'),
            'role_ids' => __('user_vendor.roles'),
            'role_ids.*' => __('user_vendor.role'),
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        $messages = [
            'translations.required' => __('user_vendor.at_least_one_translation_required'),
            'email.required' => __('user_vendor.email_required'),
            'email.email' => __('user_vendor.email_valid'),
            'email.unique' => __('user_vendor.email_already_registered'),
            'password.required' => __('user_vendor.password_required'),
            'password.min' => __('user_vendor.password_min_8'),
            'password.confirmed' => __('user_vendor.password_confirmation_mismatch'),
            'role_ids.required' => __('user_vendor.roles_required'),
            'role_ids.array' => __('user_vendor.roles_must_be_array'),
            'role_ids.min' => __('user_vendor.at_least_one_role_required'),
            'role_ids.*.required' => __('user_vendor.role_required'),
            'role_ids.*.exists' => __('user_vendor.role_invalid'),
        ];

        $languages = Language::all();
        foreach ($languages as $language) {
            $messages["translations.{$language->id}.name.required"] = 
                __('user_vendor.name_required_for_language', ['language' => $language->name]);
        }

        return $messages;
    }
}
