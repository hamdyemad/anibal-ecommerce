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
            'media_type' => 'nullable|in:image,video',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'video' => 'nullable|file|mimes:mp4,mov,avi,wmv,flv,webm|max:51200',
            'sort_order' => 'nullable|integer',
            'active' => 'nullable|boolean',
            'translations' => 'nullable|array',
            'translations.*.title' => 'nullable|string|max:255',
            'translations.*.description' => 'nullable|string',
        ];
    }

    public function attributes(): array
    {
        return [
            'slider_link' => __('systemsetting::sliders.slider_link'),
            'media_type' => __('systemsetting::sliders.media_type'),
            'image' => __('systemsetting::sliders.slider_image'),
            'video' => __('systemsetting::sliders.slider_video'),
            'sort_order' => __('systemsetting::sliders.sort_order'),
            'active' => __('systemsetting::sliders.status'),
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'active' => $this->has('active') ? (int) $this->active : 0,
            'media_type' => $this->input('media_type', 'image'),
        ]);
    }
}
