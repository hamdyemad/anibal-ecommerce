<?php

namespace Modules\Order\app\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class AddToWishlistRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Accept product_id from frontend but map to vendor_product_id
        if ($this->has('product_id') && !$this->has('vendor_product_id')) {
            $this->merge([
                'vendor_product_id' => $this->input('product_id'),
            ]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'vendor_product_id' => 'required|integer|exists:vendor_products,id',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'vendor_product_id.required' => __('validation.vendor_product_id_required'),
            'vendor_product_id.integer' => __('validation.vendor_product_id_integer'),
            'vendor_product_id.exists' => __('validation.vendor_product_id_not_exist'),
        ];
    }
}
