<?php

namespace Modules\Order\app\Http\Requests;

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
            'customer_id' => 'required|integer|exists:customers,id',
            'customer_address_id' => 'required|integer|exists:customer_addresses,id',
            'cart_items' => 'required|array|min:1',
            'cart_items.*.category_id' => 'required|integer|exists:categories,id',
            'cart_items.*.category_name' => 'nullable|string',
            'cart_items.*.product_id' => 'required|integer',
            'cart_items.*.quantity' => 'required|integer|min:1',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'customer_id.required' => trans('shipping.customer_id_required'),
            'customer_id.exists' => trans('shipping.customer_id_not_found'),
            'customer_address_id.required' => trans('shipping.address_id_required'),
            'customer_address_id.exists' => trans('shipping.address_not_found'),
            'cart_items.required' => trans('shipping.cart_items_required'),
            'cart_items.min' => trans('shipping.cart_items_min'),
            'cart_items.*.category_id.required' => trans('shipping.category_id_required'),
            'cart_items.*.category_id.exists' => trans('shipping.category_id_not_found'),
            'cart_items.*.product_id.required' => trans('shipping.product_id_required'),
            'cart_items.*.quantity.required' => trans('shipping.quantity_required'),
            'cart_items.*.quantity.min' => trans('shipping.quantity_min'),
        ];
    }
}
