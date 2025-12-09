<?php

namespace Modules\Order\app\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;
use Modules\CatalogManagement\app\Models\VendorProduct;
use Modules\Order\app\Models\Cart;

class AddToCartRequest extends FormRequest
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
            'quantity' => ['required', 'integer', 'min:1'],
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
            'quantity.required' => __('validation.quantity_required'),
            'quantity.min' => __('validation.quantity_min'),
            'type.required' => __('validation.type_required'),
            'type.in' => __('validation.type_invalid'),
            'bundle_id.exists' => __('validation.bundle_id_not_exist'),
            'occasion_id.exists' => __('validation.occasion_id_not_exist'),
        ];
    }

    /**
     * Validate quantity against vendor product max_per_order
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($validator) {
            $vendorProductId = $this->input('vendor_product_id');
            $quantity = $this->input('quantity');

            // Get vendor product and check max_per_order
            $vendorProduct = VendorProduct::find($vendorProductId);
            if (!$vendorProduct) {
                $validator->errors()->add('vendor_product_id', __('validation.vendor_product_id_not_exist'));
                return;
            }

            // Check if quantity exceeds max_per_order
            if ($quantity > $vendorProduct->max_per_order) {
                $validator->errors()->add(
                    'quantity',
                    __('validation.quantity_exceeds_max_per_order', [
                        'max' => $vendorProduct->max_per_order
                    ])
                );
            }
        });
    }
}
