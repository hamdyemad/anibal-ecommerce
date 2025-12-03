<?php

namespace Modules\Order\app\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateFulfillmentRequest extends FormRequest
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
            'order_product_id' => 'required|exists:order_products,id',
            'region_id' => 'required|exists:regions,id',
            'allocated_quantity' => 'required|integer|min:1',
            'notes' => 'nullable|string',
        ];
    }
}
