<?php

namespace Modules\Order\app\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddProductToOrderRequest extends FormRequest
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
            'vendor_product_id' => 'required|exists:vendor_products,id',
            'vendor_product_variant_id' => 'nullable|exists:vendor_product_variants,id',
            'quantity' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
        ];
    }
}
