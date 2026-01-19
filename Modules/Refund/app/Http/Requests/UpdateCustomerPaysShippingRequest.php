<?php

namespace Modules\Refund\app\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCustomerPaysShippingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return isAdmin();
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'customer_pays_return_shipping' => 'required|boolean',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'customer_pays_return_shipping.boolean' => trans('refund::refund.validation.customer_pays_return_shipping_boolean'),
        ];
    }
}
