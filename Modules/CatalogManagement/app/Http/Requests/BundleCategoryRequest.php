<?php

namespace Modules\CatalogManagement\app\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BundleCategoryRequest extends FormRequest
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
        $bundleCategoryId = $this->route('bundle_category') ? $this->route('bundle_category')->id : null;

        return [
            // Basic fields
            'active' => 'boolean',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',

            // Translation fields
            'translations' => 'required|array',
            'translations.*.name' => 'required|string|max:255',
            'translations.*.seo_title' => 'nullable|string|max:255',
            'translations.*.seo_description' => 'nullable|string|max:500',
            'translations.*.seo_keywords' => 'nullable|string|max:500',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'translations.required' => trans('catalogmanagement::bundle_category.translations_required'),
            'translations.*.name.required' => trans('catalogmanagement::bundle_category.name_required'),
            'translations.*.name.string' => trans('catalogmanagement::bundle_category.name_must_be_string'),
            'translations.*.name.max' => trans('catalogmanagement::bundle_category.name_max_length'),
            'translations.*.seo_title.string' => trans('catalogmanagement::bundle_category.seo_title_must_be_string'),
            'translations.*.seo_title.max' => trans('catalogmanagement::bundle_category.seo_title_max_length'),
            'translations.*.seo_description.string' => trans('catalogmanagement::bundle_category.seo_description_must_be_string'),
            'translations.*.seo_description.max' => trans('catalogmanagement::bundle_category.seo_description_max_length'),
            'translations.*.seo_keywords.string' => trans('catalogmanagement::bundle_category.seo_keywords_must_be_string'),
            'translations.*.seo_keywords.max' => trans('catalogmanagement::bundle_category.seo_keywords_max_length'),
            'image.image' => trans('catalogmanagement::bundle_category.image_must_be_image'),
            'image.mimes' => trans('catalogmanagement::bundle_category.image_invalid_format'),
            'image.max' => trans('catalogmanagement::bundle_category.image_max_size'),
            'active.boolean' => trans('catalogmanagement::bundle_category.active_must_be_boolean'),
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'translations.*.name' => trans('catalogmanagement::bundle_category.name'),
            'translations.*.seo_title' => trans('catalogmanagement::bundle_category.seo_title'),
            'translations.*.seo_description' => trans('catalogmanagement::bundle_category.seo_description'),
            'translations.*.seo_keywords' => trans('catalogmanagement::bundle_category.seo_keywords'),
            'image' => trans('catalogmanagement::bundle_category.image'),
            'active' => trans('catalogmanagement::bundle_category.activation'),
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Convert active checkbox to boolean
        $this->merge([
            'active' => $this->boolean('active'),
        ]);
    }
}
