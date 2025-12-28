<?php

namespace Modules\Accounting\app\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Models\Language;

class UpdateExpenseItemRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $rules = [
            'translations' => 'required|array',
            'active' => 'nullable|in:1'
        ];

        // Add validation for each language
        $languages = Language::all();
        foreach ($languages as $language) {
            $rules["translations.{$language->id}.name"] = 'required|string|max:255';
        }

        return $rules;
    }

    public function messages()
    {
        $messages = [
            'translations.required' => __('validation.required', ['attribute' => __('accounting.category_name')]),
        ];

        // Add messages for each language
        $languages = Language::all();
        foreach ($languages as $language) {
            $messages["translations.{$language->id}.name.required"] = __('validation.required', [
                'attribute' => __('accounting.category_name') . ' (' . $language->name . ')'
            ]);
        }

        return $messages;
    }

    protected function failedValidation(Validator $validator)
    {
        if ($this->ajax() || $this->wantsJson()) {
            throw new HttpResponseException(response()->json([
                'success' => false,
                'message' => __('common.validation_error'),
                'errors' => $validator->errors()
            ], 422));
        }

        parent::failedValidation($validator);
    }
}

