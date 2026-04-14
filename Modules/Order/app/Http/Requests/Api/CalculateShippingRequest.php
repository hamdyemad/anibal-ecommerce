<?php

namespace Modules\Order\app\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class CalculateShippingRequest extends FormRequest
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
        return [
            'customer_address_id' => 'nullable|integer|exists:customer_addresses,id',
            'city_id' => 'nullable|integer|exists:cities,id',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // Require at least one: customer_address_id or city_id
            if (!$this->input('customer_address_id') && !$this->input('city_id')) {
                $validator->errors()->add('city_id', trans('shipping.address_or_city_required'));
            }
        });
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'customer_address_id.exists' => trans('shipping.address_not_found'),
            'city_id.exists' => trans('shipping.city_not_found'),
        ];
    }
}
