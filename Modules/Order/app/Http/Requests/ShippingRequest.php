<?php

namespace Modules\Order\app\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ShippingRequest extends FormRequest
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
        // Simplified - City-based shipping only (no categories/departments)
        $rules = [
            'translations' => 'required|array',
            'translations.*.name' => 'required|string|max:255',
            'cost' => 'required|numeric|min:0',
            'active' => 'nullable|boolean',
            'city_ids' => 'required|array|min:1',
            'city_ids.*' => 'required|exists:cities,id',
        ];

        return $rules;
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'translations.required' => trans('shipping.translations_required'),
            'translations.*.name.required' => trans('shipping.name_required'),
            'translations.*.name.max' => trans('shipping.name_max_255'),
            'cost.required' => trans('shipping.cost_required'),
            'city_ids.required' => trans('shipping.city_ids_required'),
            'city_ids.min' => trans('shipping.city_ids_min'),
            'city_ids.*.exists' => trans('shipping.city_id_not_exist'),
        ];
    }
}
