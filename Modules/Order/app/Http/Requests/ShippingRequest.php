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
        return [
            'translations' => 'required|array',
            'translations.*.name' => 'required|string|max:255',
            'cost' => 'required|numeric|min:0',
            'active' => 'nullable|boolean',
            'city_id' => 'required|exists:cities,id',
            'category_id' => 'required|exists:categories,id',
            'country_id' => 'required|exists:countries,id',
        ];
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
            'city_id.required' => trans('shipping.city_id_required'),
            'category_id.required' => trans('shipping.category_id_required'),
            'country_id.required' => trans('shipping.country_id_required'),
        ];
    }
}
