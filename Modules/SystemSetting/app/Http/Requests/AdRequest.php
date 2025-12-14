<?php

namespace Modules\SystemSetting\app\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Language;
use Illuminate\Validation\Rule;

class AdRequest extends FormRequest
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
            'position' => 'required|in:header,footer,sidebar,home_banner,product_page,category_page',
            'link' => 'nullable|url|max:500',
            'translations' => 'required|array',
            'translations.*.title' => 'required|string|max:255',
            'translations.*.subtitle' => 'nullable|string|max:500',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'remove_image' => 'nullable|boolean',
            'active' => 'nullable|boolean',
        ];

        return $rules;
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        $attributes = [
            'position' => __('systemsetting::ads.position'),
            'link' => __('systemsetting::ads.link'),
            'image' => __('systemsetting::ads.ad_image'),
        ];

        // Add dynamic attributes for each language
        $languages = Language::all();
        foreach ($languages as $language) {
            $langCode = $language->code == 'ar' ? 'arabic' : 'english';
            $attributes['translations.' . $language->id . '.title'] = __('systemsetting::ads.title_' . $langCode);
            $attributes['translations.' . $language->id . '.subtitle'] = __('systemsetting::ads.subtitle_' . $langCode);
        }

        return $attributes;
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        $messages = [
            'position.required' => __('systemsetting::ads.validation.position_required'),
            'link.url' => __('systemsetting::ads.validation.link_url'),
        ];

        // Add dynamic messages for each language
        $languages = Language::all();
        foreach ($languages as $language) {
            $langCode = $language->code == 'ar' ? 'ar' : 'en';
            $messages['translations.' . $language->id . '.title.required'] = __('systemsetting::ads.validation.title_' . $langCode . '_required');
        }

        return $messages;
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Convert active value to integer
        $this->merge([
            'active' => (int) $this->input('active', 0),
        ]);
    }
}
