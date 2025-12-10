<?php

namespace Modules\SystemSetting\app\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PointsSettingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'currency_id' => 'required|exists:currencies,id',
            'is_active' => 'nullable|boolean',
            'points_value' => 'nullable|numeric|min:0|max:999999.99',
            'welcome_points' => 'nullable|numeric|min:0|max:999999.99',
        ];
    }

    public function messages(): array
    {
        return [
            'currency_id.required' => __('validation.required', ['attribute' => __('systemsetting::points.currency')]),
            'currency_id.exists' => __('validation.exists', ['attribute' => __('systemsetting::points.currency')]),
            'points_value.required' => __('validation.required', ['attribute' => __('systemsetting::points.points_value')]),
            'points_value.numeric' => __('validation.numeric', ['attribute' => __('systemsetting::points.points_value')]),
            'points_value.min' => __('validation.min.numeric', ['attribute' => __('systemsetting::points.points_value'), 'min' => 0]),
            'points_value.max' => __('validation.max.numeric', ['attribute' => __('systemsetting::points.points_value'), 'max' => 999999.99]),
            'welcome_points.required' => __('validation.required', ['attribute' => __('systemsetting::points.welcome_points')]),
            'welcome_points.numeric' => __('validation.numeric', ['attribute' => __('systemsetting::points.welcome_points')]),
            'welcome_points.min' => __('validation.min.numeric', ['attribute' => __('systemsetting::points.welcome_points'), 'min' => 0]),
            'welcome_points.max' => __('validation.max.numeric', ['attribute' => __('systemsetting::points.welcome_points'), 'max' => 999999.99]),
        ];
    }

    protected function prepareForValidation()
    {
        if ($this->has('is_active')) {
            $this->merge([
                'is_active' => (bool) $this->is_active,
            ]);
        }
    }
}
