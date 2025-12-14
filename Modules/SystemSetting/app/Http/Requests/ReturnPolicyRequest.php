<?php

namespace Modules\SystemSetting\app\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ReturnPolicyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'description' => 'nullable|array',
            'description.*' => 'nullable|array',
            'description.*.*' => 'nullable|string',
        ];
    }

    public function attributes(): array
    {
        return [
            'description_en' => __('systemsetting::return-policy.description_en'),
            'description_ar' => __('systemsetting::return-policy.description_ar'),
        ];
    }
}
