<?php

namespace Modules\CatalogManagement\app\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class StoreReviewRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'review' => 'required|string|max:1000',
            'star' => 'required|integer|min:1|max:5',
        ];
    }

    public function messages(): array
    {
        return [
            'review.required' => __('validation.required', ['attribute' => 'review']),
            'review.string' => __('validation.string', ['attribute' => 'review']),
            'review.max' => __('validation.max.string', ['attribute' => 'review', 'max' => 1000]),
            'star.required' => __('validation.required', ['attribute' => 'star']),
            'star.integer' => __('validation.integer', ['attribute' => 'star']),
            'star.min' => __('validation.min.numeric', ['attribute' => 'star', 'min' => 1]),
            'star.max' => __('validation.max.numeric', ['attribute' => 'star', 'max' => 5]),
        ];
    }
}
