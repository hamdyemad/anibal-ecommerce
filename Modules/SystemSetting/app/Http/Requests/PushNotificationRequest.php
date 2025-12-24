<?php

namespace Modules\SystemSetting\app\Http\Requests;

use App\Models\Language;
use Illuminate\Foundation\Http\FormRequest;

class PushNotificationRequest extends FormRequest
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
            'type' => 'required|in:all,specific',
            'customer_ids' => 'required_if:type,specific|array',
            'customer_ids.*' => 'exists:customers,id',
            'translations' => 'required|array',
            'translations.*.title' => 'required|string|max:255',
            'translations.*.description' => 'required|string|max:5000',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        $attributes = [
            'type' => __('systemsetting::push-notification.notification_type'),
            'customer_ids' => __('systemsetting::push-notification.select_customers'),
            'image' => __('systemsetting::push-notification.image'),
        ];

        // Add dynamic attributes for each language
        $languages = Language::all();
        foreach ($languages as $language) {
            $langName = $language->code == 'ar' ? 'Arabic' : 'English';
            $attributes['translations.' . $language->id . '.title'] = __('systemsetting::push-notification.title') . ' (' . $langName . ')';
            $attributes['translations.' . $language->id . '.description'] = __('systemsetting::push-notification.description') . ' (' . $langName . ')';
        }

        return $attributes;
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'type.required' => __('systemsetting::push-notification.validation.type_required'),
            'type.in' => __('systemsetting::push-notification.validation.type_invalid'),
            'customer_ids.required_if' => __('systemsetting::push-notification.validation.customers_required'),
            'translations.required' => __('systemsetting::push-notification.validation.translations_required'),
            'translations.*.title.required' => __('systemsetting::push-notification.validation.title_required'),
            'translations.*.description.required' => __('systemsetting::push-notification.validation.description_required'),
            'image.image' => __('systemsetting::push-notification.validation.image_invalid'),
            'image.max' => __('systemsetting::push-notification.validation.image_max'),
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Clean up customer_ids if type is 'all'
        if ($this->input('type') === 'all') {
            $this->merge([
                'customer_ids' => [],
            ]);
        }
    }

    /**
     * Get validated data with extracted translations
     */
    public function validatedWithTranslations(): array
    {
        $validated = $this->validated();
        $translations = $this->input('translations', []);
        $languages = Language::all()->keyBy('id');

        $titleEn = '';
        $titleAr = '';
        $descriptionEn = '';
        $descriptionAr = '';

        foreach ($translations as $langId => $fields) {
            $lang = $languages->get((int) $langId);
            if ($lang) {
                if ($lang->code === 'en') {
                    $titleEn = $fields['title'] ?? '';
                    $descriptionEn = $fields['description'] ?? '';
                } elseif ($lang->code === 'ar') {
                    $titleAr = $fields['title'] ?? '';
                    $descriptionAr = $fields['description'] ?? '';
                }
            }
        }

        return array_merge($validated, [
            'title_en' => $titleEn,
            'title_ar' => $titleAr,
            'description_en' => $descriptionEn,
            'description_ar' => $descriptionAr,
        ]);
    }
}
