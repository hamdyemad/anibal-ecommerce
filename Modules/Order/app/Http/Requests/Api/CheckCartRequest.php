<?php

namespace Modules\Order\app\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class CheckCartRequest extends FormRequest
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
        return [
            'vendor_product_id' => ['required', 'integer', 'exists:vendor_products,id'],
            'vendor_product_variant_id' => ['required', 'integer', 'exists:vendor_product_variants,id'],
            'type' => ['required', 'string', 'in:product,bundle,occasion'],
            'bundle_id' => ['required_if:type,bundle', 'integer', 'exists:bundles,id'],
            'occasion_id' => ['required_if:type,occasion', 'integer', 'exists:occasions,id'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'vendor_product_id.required' => __('validation.vendor_product_id_required'),
            'vendor_product_id.exists' => __('validation.vendor_product_id_not_exist'),
            'vendor_product_variant_id.exists' => __('validation.vendor_product_variant_id_not_exist'),
            'type.in' => __('validation.type_invalid'),
            'bundle_id.exists' => __('validation.bundle_id_not_exist'),
            'occasion_id.exists' => __('validation.occasion_id_not_exist'),
        ];
    }
}
