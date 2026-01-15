<?php

namespace Modules\Customer\app\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
            'last_name' => 'nullable|string|max:255',
            'email' => 'required|email|unique:customers,email',
            'password' => 'required|string|min:8|confirmed',
            'phone' => [
                'nullable',
                'string',
                'unique:customers,phone',
                // Phone length validation using custom rule
                function ($attribute, $value, $fail) {
                    if ($value && $this->country_id) {
                        $this->validatePhoneLength($value, $fail);
                    }
                },
            ],
            'lang' => 'nullable|string|in:en,ar',
            'country_id' => 'required|exists:countries,id',
            // City must exist AND belong to the specified country (single query)
            'city_id' => [
                'required',
                Rule::exists('cities', 'id')->where(function ($query) {
                    if ($this->country_id) {
                        $query->where('country_id', $this->country_id);
                    }
                }),
            ],
            // Region must exist AND belong to the specified city (single query)
            'region_id' => [
                'required',
                Rule::exists('regions', 'id')->where(function ($query) {
                    if ($this->city_id) {
                        $query->where('city_id', $this->city_id);
                    }
                }),
            ],
            'gender' => 'required|in:male,female',
        ];
    }

    /**
     * Validate phone length against country's phone_length setting
     * Uses cached country data to avoid repeated queries
     */
    protected function validatePhoneLength(string $phone, callable $fail): void
    {
        // Cache the country lookup within the request lifecycle
        static $countryCache = [];
        
        $countryId = $this->country_id;
        
        if (!isset($countryCache[$countryId])) {
            $countryCache[$countryId] = \Modules\AreaSettings\app\Models\Country::find($countryId, ['id', 'phone_length']);
        }
        
        $country = $countryCache[$countryId];
        
        if ($country && $country->phone_length) {
            $phoneDigits = preg_replace('/\D/', '', $phone);
            if (strlen($phoneDigits) !== $country->phone_length) {
                $fail(trans('customer::customer.phone_length_invalid', [
                    'length' => $country->phone_length
                ]));
            }
        }
    }

    /**
     * Get custom error messages for validation rules
     */
    public function messages(): array
    {
        return [
            'city_id.exists' => trans('customer::customer.city_must_belong_to_country'),
            'region_id.exists' => trans('customer::customer.region_must_belong_to_city'),
        ];
    }
}
