<?php

namespace Modules\SystemSetting\app\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FeatureRequest extends FormRequest
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
            'active' => 'nullable|boolean',
            'logo' => $this->isMethod('post') ? 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048' : 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'translations' => 'required|array',
            'translations.*.title' => 'required|string|max:255',
            'translations.*.subtitle' => 'nullable|string|max:500',
        ];

        return $rules;
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        $attributes = [
            'logo' => __('systemsetting::features.logo'),
            'active' => __('systemsetting::features.status'),
        ];

        // Add dynamic attributes for translations
        foreach ($this->input('translations', []) as $langId => $translation) {
            $langName = \App\Models\Language::find($langId)->name ?? "Language $langId";
            $attributes["translations.$langId.title"] = __('systemsetting::features.title') . " ($langName)";
            $attributes["translations.$langId.subtitle"] = __('systemsetting::features.subtitle') . " ($langName)";
        }

        return $attributes;
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'logo.required' => __('systemsetting::features.logo_required'),
            'logo.image' => __('systemsetting::features.logo_must_be_image'),
            'translations.*.title.required' => __('systemsetting::features.title_required'),
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'active' => $this->has('active') ? (int) $this->active : 0,
        ]);
    }
}
