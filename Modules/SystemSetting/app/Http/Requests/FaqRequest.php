<?php

namespace Modules\SystemSetting\app\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FaqRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'active' => 'nullable|boolean',
            'translations' => 'required|array',
            'translations.*.question' => 'required|string|max:500',
            'translations.*.answer' => 'required|string',
        ];
    }

    public function attributes(): array
    {
        $attributes = [
            'active' => __('systemsetting::faqs.status'),
        ];

        foreach ($this->input('translations', []) as $langId => $translation) {
            $langName = \App\Models\Language::find($langId)->name ?? "Language $langId";
            $attributes["translations.$langId.question"] = __('systemsetting::faqs.question') . " ($langName)";
            $attributes["translations.$langId.answer"] = __('systemsetting::faqs.answer') . " ($langName)";
        }

        return $attributes;
    }

    public function messages(): array
    {
        return [
            'translations.*.question.required' => __('systemsetting::faqs.question_required'),
            'translations.*.answer.required' => __('systemsetting::faqs.answer_required'),
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'active' => $this->has('active') ? (int) $this->active : 0,
        ]);
    }
}
