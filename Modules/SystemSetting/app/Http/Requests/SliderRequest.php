<?php

namespace Modules\SystemSetting\app\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SliderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'slider_link' => 'nullable|url',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'sort_order' => 'nullable|integer',
            'active' => 'nullable|boolean',
        ];
    }

    public function attributes(): array
    {
        return [
            'slider_link' => __('systemsetting::sliders.slider_link'),
            'image' => __('systemsetting::sliders.slider_image'),
            'sort_order' => __('systemsetting::sliders.sort_order'),
            'active' => __('systemsetting::sliders.status'),
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'active' => $this->has('active') ? (int) $this->active : 0,
        ]);
    }
}
