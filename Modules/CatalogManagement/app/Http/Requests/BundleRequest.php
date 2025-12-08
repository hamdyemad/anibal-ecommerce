<?php

namespace Modules\CatalogManagement\app\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BundleRequest extends FormRequest
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
            'vendor_id' => 'required|exists:vendors,id',
            'bundle_category_id' => 'required|exists:bundle_categories,id',
            'sku' => 'required|string|unique:bundles,sku' . ($this->bundle ? ',' . $this->bundle->id : ''),
            'is_active' => 'nullable|boolean',
            'translations' => 'required|array',
            'translations.*.name' => 'required|string|max:255',
            'translations.*.description' => 'nullable|string',
            'translations.*.seo_title' => 'nullable|string|max:255',
            'translations.*.seo_description' => 'nullable|string|max:500',
            'translations.*.seo_keywords' => 'nullable|string',
            'attachments' => 'nullable|array',
            'attachments.*.path' => 'nullable|string',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'vendor_id.required' => 'Vendor is required',
            'vendor_id.exists' => 'Selected vendor does not exist',
            'bundle_category_id.required' => 'Bundle category is required',
            'bundle_category_id.exists' => 'Selected category does not exist',
            'sku.required' => 'SKU is required',
            'sku.unique' => 'SKU must be unique',
            'translations.required' => 'Translations are required',
            'translations.*.name.required' => 'Bundle name is required for each language',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Add country_id from session
        $this->merge([
            'country_id' => session('country_id'),
        ]);
    }
}
