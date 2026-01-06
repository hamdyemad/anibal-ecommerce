<?php

namespace Modules\Customer\app\Http\Requests\Dashboard;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CustomerRequest extends FormRequest
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
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $customerId = $this->route('customer') ?? $this->route('id');
        $isUpdate = $this->isMethod('PUT') || $this->isMethod('PATCH') || !empty($customerId);

        return [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                Rule::unique('customers', 'email')->ignore($customerId)->whereNull('deleted_at'),
            ],
            'phone' => ['required', 'string', 'max:20', function ($attribute, $value, $fail) {
                // Get city_id from request to find country's phone_length
                $cityId = $this->input('city_id');
                if ($cityId) {
                    $city = \Modules\AreaSettings\app\Models\City::with('country')->find($cityId);
                    if ($city && $city->country && $city->country->phone_length) {
                        $phoneDigits = preg_replace('/\D/', '', $value);
                        if (strlen($phoneDigits) !== $city->country->phone_length) {
                            $fail(trans('customer::customer.phone_length_invalid', [
                                'length' => $city->country->phone_length
                            ]));
                        }
                    }
                }
            }],
            'date_of_birth' => 'nullable|date|before:today',
            'gender' => 'required|in:male,female',
            'city_id' => 'required|exists:cities,id',
            'region_id' => 'required|exists:regions,id',
            'gender' => 'required|in:male,female',
            'password' => $isUpdate ? 'nullable|string|min:8|confirmed' : 'required|string|min:8|confirmed',
            'status' => 'boolean',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'first_name.required' => __('customer::customer.first_name') . ' ' . __('validation.required'),
            'first_name.string' => __('customer::customer.first_name') . ' ' . __('validation.string'),
            'first_name.max' => __('customer::customer.first_name') . ' ' . __('validation.max.string', ['max' => 255]),

            'last_name.required' => __('customer::customer.last_name') . ' ' . __('validation.required'),
            'last_name.string' => __('customer::customer.last_name') . ' ' . __('validation.string'),
            'last_name.max' => __('customer::customer.last_name') . ' ' . __('validation.max.string', ['max' => 255]),

            'email.required' => __('customer::customer.email') . ' ' . __('validation.required'),
            'email.email' => __('customer::customer.email') . ' ' . __('validation.email'),
            'email.unique' => __('customer::customer.email') . ' ' . __('validation.unique'),

            'phone.required' => __('customer::customer.phone') . ' ' . __('validation.required'),
            'phone.string' => __('customer::customer.phone') . ' ' . __('validation.string'),
            'phone.max' => __('customer::customer.phone') . ' ' . __('validation.max.string', ['max' => 20]),

            'date_of_birth.date' => __('customer::customer.date_of_birth') . ' ' . __('validation.date'),
            'date_of_birth.before' => __('customer::customer.date_of_birth') . ' ' . __('validation.before', ['date' => 'today']),

            'gender.required' => __('customer::customer.gender') . ' ' . __('validation.required'),
            'gender.in' => __('customer::customer.gender') . ' ' . __('validation.in'),

            'city_id.required' => __('customer::customer.city') . ' ' . __('validation.required'),
            'city_id.exists' => __('customer::customer.city') . ' ' . __('validation.exists'),

            'region_id.required' => __('customer::customer.region') . ' ' . __('validation.required'),
            'region_id.exists' => __('customer::customer.region') . ' ' . __('validation.exists'),

            'password.required' => __('customer::customer.password') . ' ' . __('validation.required'),
            'password.string' => __('customer::customer.password') . ' ' . __('validation.string'),
            'password.min' => __('customer::customer.password') . ' ' . __('validation.min.string', ['min' => 8]),
            'password.confirmed' => __('customer::customer.password') . ' ' . __('validation.confirmed'),

            'status.boolean' => __('customer::customer.status') . ' ' . __('validation.boolean'),
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'first_name' => __('customer::customer.first_name'),
            'last_name' => __('customer::customer.last_name'),
            'email' => __('customer::customer.email'),
            'phone' => __('customer::customer.phone'),
            'date_of_birth' => __('customer::customer.date_of_birth'),
            'gender' => __('customer::customer.gender'),
            'city_id' => __('customer::customer.city'),
            'region_id' => __('customer::customer.region'),
            'password' => __('customer::customer.password'),
            'status' => __('customer::customer.status'),
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'status' => $this->boolean('status'),
        ]);
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // Validate that region belongs to the city if both are provided
            if ($this->city_id && $this->region_id) {
                $region = \Modules\AreaSettings\app\Models\Region::where('id', $this->region_id)
                    ->where('city_id', $this->city_id)
                    ->first();

                if (!$region) {
                    $validator->errors()->add('region_id', trans('customer::customer.region_must_belong_to_city'));
                }
            }
        });

        return $validator;
    }

    /**
     * Get the validated data from the request.
     *
     * @return array<string, mixed>
     */
    public function validated($key = null, $default = null)
    {
        $validated = parent::validated($key, $default);

        // Remove password if it's empty (for updates)
        if (empty($validated['password'])) {
            unset($validated['password']);
        }

        return $validated;
    }
}
