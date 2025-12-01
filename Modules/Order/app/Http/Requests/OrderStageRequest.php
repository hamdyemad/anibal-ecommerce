<?php

namespace Modules\Order\app\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OrderStageRequest extends FormRequest
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
        // Get the order stage ID from route parameter (for update) or use null (for create)
        $orderStageId = $this->route('order_stage') ?? $this->route('id');

        return [
            // Basic fields
            'active' => 'boolean',
            'color' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'sort_order' => 'nullable|integer|min:0',

            // Translation fields
            'translations' => 'required|array',
            'translations.*.name' => 'required|string|max:255',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        $messages = [
            'color.required' => __('order::order_stage.color_required'),
            'color.regex' => __('order::order_stage.color_invalid_format'),
            'translations.required' => __('order::order_stage.translations_required'),
        ];

        // Get all languages
        $languages = \App\Models\Language::all();

        // Generate language-specific messages
        foreach ($languages as $language) {
            $langName = $language->name;
            $langCode = $language->code;

            $messages["translations.{$language->id}.name.required"] = __('order::order_stage.name_required') . " ({$langName})";
            $messages["translations.{$language->id}.name.string"] = __('order::order_stage.name_must_be_string') . " ({$langName})";
            $messages["translations.{$language->id}.name.max"] = __('order::order_stage.name_max_length') . " ({$langName})";
        }

        return $messages;
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'active' => $this->has('active') ? 1 : 0,
        ]);
    }
}
