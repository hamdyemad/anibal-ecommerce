<?php

namespace Modules\Order\app\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateOrderRequest extends FormRequest
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
            'customer_name' => 'sometimes|string|max:255',
            'customer_email' => 'sometimes|email|max:255',
            'customer_address' => 'sometimes|string',
            'customer_phone' => 'sometimes|string|max:20',
            'order_from' => 'sometimes|in:ios,android,web',
            'payment_type' => 'sometimes|in:cash_on_delivery,online',
            'customer_promo_code_title' => 'nullable|string|max:255',
            'customer_promo_code_value' => 'nullable|numeric|min:0',
            'customer_promo_code_type' => 'nullable|in:percentage,fixed',
            'shipping' => 'sometimes|numeric|min:0',
            'stage_id' => 'nullable|exists:order_stages,id',
            'country_id' => 'nullable|exists:countries,id',
            'city_id' => 'nullable|exists:cities,id',
            'region_id' => 'nullable|exists:regions,id',
        ];
    }
}
