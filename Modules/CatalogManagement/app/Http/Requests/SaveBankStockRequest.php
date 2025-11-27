<?php

namespace Modules\CatalogManagement\app\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\CatalogManagement\app\Models\Product;

class SaveBankStockRequest extends FormRequest
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
        $rules = [
            'product_id' => 'required|integer|exists:products,id',
            'vendor_id' => 'required|integer|exists:vendors,id',
        ];

        // Add configuration-specific rules
        $product = Product::find($this->product_id);
        if ($product->configuration_type == 'simple') {
            $rules = array_merge($rules, $this->getSimpleProductRules());
        } else {
            $rules = array_merge($rules, $this->getVariantProductRules());
        }

        return $rules;
    }

    /**
     * Get validation rules for simple products
     */
    private function getSimpleProductRules(): array
    {
        $rules = [
            'sku' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'tax_id' => 'required|integer|exists:taxes,id',
            'has_discount' => 'nullable',
            'stocks' => 'required|array|min:1',
            'stocks.*.region_id' => 'required|integer|exists:regions,id',
            'stocks.*.quantity' => 'required|integer|min:0',
        ];

        // Add conditional validation for discount fields when switcher is ON
        if ($this->input('has_discount') == 'on' || $this->input('has_discount') == '1' || $this->input('has_discount') == 1 || $this->input('has_discount') === true || $this->input('has_discount') === 'true') {
            $rules['price_before_discount'] = 'required|numeric|min:0|gt:price';
            $rules['discount_end_date'] = 'required|date|after:today';
        } else {
            $rules['price_before_discount'] = 'nullable|numeric|min:0';
            $rules['discount_end_date'] = 'nullable|date';
        }

        return $rules;
    }

    /**
     * Get validation rules for variant products
     */
    private function getVariantProductRules(): array
    {
        $rules = [
            'variants' => 'required|array|min:1',
            'variants.*.id' => 'nullable|integer',
            'variants.*.sku' => 'required|string|max:255',
            'variants.*.price' => 'required|numeric|min:0',
            'variants.*.tax_id' => 'required|integer|exists:taxes,id',
            'variants.*.has_discount' => 'nullable',
            'variants.*.variant_configuration_id' => 'nullable|integer',
            'variants.*.stocks' => 'required|array|min:1',
            'variants.*.stocks.*.region_id' => 'required|integer|exists:regions,id',
            'variants.*.stocks.*.quantity' => 'required|integer|min:0',
        ];

        // Add conditional validation for each variant's discount fields when switcher is ON
        if ($this->has('variants') && is_array($this->input('variants'))) {
            foreach ($this->input('variants') as $index => $variant) {
                $hasDiscount = isset($variant['has_discount']) &&
                              ($variant['has_discount'] == 'on' ||
                               $variant['has_discount'] == '1' ||
                               $variant['has_discount'] == 1 ||
                               $variant['has_discount'] === true ||
                               $variant['has_discount'] === 'true');

                if ($hasDiscount) {
                    $rules["variants.{$index}.price_before_discount"] = 'required|numeric|min:0|gt:variants.' . $index . '.price';
                    $rules["variants.{$index}.discount_end_date"] = 'required|date|after:today';
                } else {
                    $rules["variants.{$index}.price_before_discount"] = 'nullable|numeric|min:0';
                    $rules["variants.{$index}.discount_end_date"] = 'nullable|date';
                }
            }
        }

        return $rules;
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'product_id.required' => 'Product selection is required.',
            'product_id.exists' => 'Selected product does not exist.',
            'vendor_id.required' => 'Vendor selection is required.',
            'vendor_id.exists' => 'Selected vendor does not exist.',
            'configuration_type.required' => 'Product configuration type is required.',
            'configuration_type.in' => 'Product configuration type must be simple or variants.',

            // Simple product messages
            'sku.required' => 'SKU is required.',
            'price.required' => 'Price is required.',
            'price.min' => 'Price must be greater than or equal to 0.',
            'tax_id.required' => 'Tax selection is required.',
            'tax_id.exists' => 'Selected tax does not exist.',
            'price_before_discount.required' => 'Price before discount is required when discount is enabled.',
            'price_before_discount.numeric' => 'Price before discount must be a valid number.',
            'price_before_discount.min' => 'Price before discount must be greater than or equal to 0.',
            'price_before_discount.gt' => 'Price before discount must be greater than the current price.',
            'discount_end_date.required' => 'Discount end date is required when discount is enabled.',
            'discount_end_date.date' => 'Discount end date must be a valid date.',
            'discount_end_date.after' => 'Discount end date must be after today.',
            'stocks.required' => 'At least one stock entry is required.',
            'stocks.min' => 'At least one stock entry is required.',
            'stocks.*.region_id.required' => 'Region selection is required for each stock entry.',
            'stocks.*.region_id.exists' => 'Selected region does not exist.',
            'stocks.*.quantity.required' => 'Quantity is required for each stock entry.',
            'stocks.*.quantity.min' => 'Quantity must be greater than or equal to 0.',

            // Variant product messages
            'variants.required' => 'At least one variant is required.',
            'variants.min' => 'At least one variant is required.',
            'variants.*.sku.required' => 'SKU is required for each variant.',
            'variants.*.price.required' => 'Price is required for each variant.',
            'variants.*.price.min' => 'Price must be greater than or equal to 0.',
            'variants.*.tax_id.required' => 'Tax selection is required for each variant.',
            'variants.*.tax_id.exists' => 'Selected tax does not exist.',
            'variants.*.price_before_discount.required' => 'Price before discount is required when discount is enabled for this variant.',
            'variants.*.price_before_discount.numeric' => 'Price before discount must be a valid number.',
            'variants.*.price_before_discount.min' => 'Price before discount must be greater than or equal to 0.',
            'variants.*.price_before_discount.gt' => 'Price before discount must be greater than the variant price.',
            'variants.*.discount_end_date.required' => 'Discount end date is required when discount is enabled for this variant.',
            'variants.*.discount_end_date.date' => 'Discount end date must be a valid date.',
            'variants.*.discount_end_date.after' => 'Discount end date must be after today.',
            'variants.*.stocks.required' => 'At least one stock entry is required for each variant.',
            'variants.*.stocks.min' => 'At least one stock entry is required for each variant.',
            'variants.*.stocks.*.region_id.required' => 'Region selection is required for each stock entry.',
            'variants.*.stocks.*.region_id.exists' => 'Selected region does not exist.',
            'variants.*.stocks.*.quantity.required' => 'Quantity is required for each stock entry.',
            'variants.*.stocks.*.quantity.min' => 'Quantity must be greater than or equal to 0.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'product_id' => 'product',
            'vendor_id' => 'vendor',
            'configuration_type' => 'configuration type',
            'tax_id' => 'tax',
            'price_before_discount' => 'price before discount',
            'discount_end_date' => 'discount end date',
            'stocks.*.region_id' => 'region',
            'stocks.*.quantity' => 'quantity',
            'variants.*.sku' => 'variant SKU',
            'variants.*.price' => 'variant price',
            'variants.*.tax_id' => 'variant tax',
            'variants.*.price_before_discount' => 'variant price before discount',
            'variants.*.discount_end_date' => 'variant discount end date',
            'variants.*.stocks.*.region_id' => 'variant stock region',
            'variants.*.stocks.*.quantity' => 'variant stock quantity',
        ];
    }
}
