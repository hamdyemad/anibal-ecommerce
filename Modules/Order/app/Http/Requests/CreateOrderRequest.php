<?php

namespace Modules\Order\app\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateOrderRequest extends FormRequest
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
            'customer_id' => 'nullable|exists:customers,id',
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'required|email|max:255',
            'customer_address' => 'required|string',
            'customer_phone' => 'required|string|max:20',
            'order_from' => 'required|in:ios,android,web',
            'payment_type' => 'required|in:cash_on_delivery,online',
            'customer_promo_code_title' => 'nullable|string|max:255',
            'customer_promo_code_value' => 'nullable|numeric|min:0',
            'customer_promo_code_type' => 'nullable|in:percentage,fixed',
            'shipping' => 'required|numeric|min:0',
            'stage_id' => 'nullable|exists:order_stages,id',
            'city_id' => 'nullable|exists:cities,id',
            'region_id' => 'nullable|exists:regions,id',
            'products' => 'required|array|min:1',
            'products.*.vendor_product_id' => 'required|exists:vendor_products,id',
            'products.*.vendor_product_variant_id' => 'nullable|exists:vendor_product_variants,id',
            'products.*.quantity' => 'required|integer|min:1',
            'products.*.price' => 'required|numeric|min:0',
        ];
    }
}
