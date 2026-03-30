<?php

namespace Modules\Order\app\Http\Requests\Api;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class CheckoutRequest extends FormRequest
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
        $isGuest = $this->input('is_guest', false);
        
        if ($isGuest) {
            // Guest checkout validation
            return [
                'is_guest' => 'required|boolean',
                'guest_first_name' => 'required|string|max:255',
                'guest_last_name' => 'required|string|max:255',
                'guest_email' => 'required|email|max:255',
                'guest_phone' => 'required|string|max:20',
                'guest_country_id' => 'required|integer|exists:countries,id',
                'guest_city_id' => 'required|integer|exists:cities,id',
                'guest_region_id' => 'nullable|integer|exists:regions,id',
                'guest_address' => 'nullable|string|max:500',
                'products' => 'required|array|min:1',
                'products.*.vendor_product_id' => 'required|integer|exists:vendor_products,id',
                'products.*.vendor_product_variant_id' => 'nullable|integer|exists:vendor_product_variants,id',
                'products.*.quantity' => 'required|integer|min:1',
                'products.*.type' => 'nullable|string|in:product,bundle,occasion',
                'products.*.bundle_id' => 'nullable|integer|exists:bundles,id',
                'products.*.occasion_id' => 'nullable|integer|exists:occasions,id',
                'promo_code_id' => 'nullable|exists:promocodes,id',
                'order_from' => Rule::in(['WEB', 'ANDROID', 'IOS', 'web', 'android', 'ios']),
                'payment_type' => Rule::in(['cash_on_delivery', 'online', 'aman']),
                'use_point' => 'nullable|boolean',
            ];
        }
        
        // Existing customer checkout validation
        return [
            'is_guest' => 'nullable|boolean',
            'customer_address_id' => 'required|integer|exists:customer_addresses,id',
            'promo_code_id' => 'nullable|exists:promocodes,id',
            'order_from' => Rule::in(['WEB', 'ANDROID', 'IOS', 'web', 'android', 'ios']),
            'payment_type' => Rule::in(['cash_on_delivery', 'online', 'aman']),
            'use_point' => 'required|boolean',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'customer_address_id.required' => __('validation.user_address_id_required'),
            'customer_address_id.integer' => __('validation.user_address_id_integer'),
            'customer_address_id.exists' => __('validation.user_address_id_exists'),
            'promo_code_id.exists' => __('validation.promo_code_id_exists'),
            'order_from.in' => __('validation.order_from_in'),
            'payment_type.in' => __('validation.payment_type_in'),
            'use_point.required' => __('validation.use_point_required'),
            'use_point.boolean' => __('validation.use_point_boolean'),
            
            // Guest validation messages
            'guest_first_name.required' => __('validation.guest_first_name_required'),
            'guest_last_name.required' => __('validation.guest_last_name_required'),
            'guest_email.required' => __('validation.guest_email_required'),
            'guest_email.email' => __('validation.guest_email_email'),
            'guest_phone.required' => __('validation.guest_phone_required'),
            'guest_country_id.required' => __('validation.guest_country_id_required'),
            'guest_country_id.exists' => __('validation.guest_country_id_exists'),
            'guest_city_id.required' => __('validation.guest_city_id_required'),
            'guest_city_id.exists' => __('validation.guest_city_id_exists'),
            'guest_region_id.exists' => __('validation.guest_region_id_exists'),
            
            // Products validation messages
            'products.required' => __('validation.products_required'),
            'products.array' => __('validation.products_array'),
            'products.min' => __('validation.products_min'),
            'products.*.vendor_product_id.required' => __('validation.product_id_required'),
            'products.*.vendor_product_id.exists' => __('validation.product_id_exists'),
            'products.*.quantity.required' => __('validation.product_quantity_required'),
            'products.*.quantity.min' => __('validation.product_quantity_min'),
        ];
    }
}
