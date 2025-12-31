<?php

namespace Modules\Order\app\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\SystemSetting\app\Models\SiteInformation;

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
        $shippingSettings = SiteInformation::first();
        
        $rules = [
            'translations' => 'required|array',
            'translations.*.name' => 'required|string|max:255',
            'cost' => 'required|numeric|min:0',
            'active' => 'nullable|boolean',
            'city_ids' => 'required|array|min:1',
            'city_ids.*' => 'required|exists:cities,id',
        ];

        // Add validation based on settings
        if ($shippingSettings?->shipping_allow_departments) {
            $rules['department_ids'] = 'required|array|min:1';
            $rules['department_ids.*'] = 'required|exists:departments,id';
        } else {
            $rules['department_ids'] = 'nullable|array';
            $rules['department_ids.*'] = 'nullable|exists:departments,id';
        }

        if ($shippingSettings?->shipping_allow_categories) {
            $rules['category_ids'] = 'required|array|min:1';
            $rules['category_ids.*'] = 'required|exists:categories,id';
        } else {
            $rules['category_ids'] = 'nullable|array';
            $rules['category_ids.*'] = 'nullable|exists:categories,id';
        }

        if ($shippingSettings?->shipping_allow_sub_categories) {
            $rules['sub_category_ids'] = 'required|array|min:1';
            $rules['sub_category_ids.*'] = 'required|exists:sub_categories,id';
        } else {
            $rules['sub_category_ids'] = 'nullable|array';
            $rules['sub_category_ids.*'] = 'nullable|exists:sub_categories,id';
        }

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
            'category_ids.required' => trans('shipping.category_ids_required'),
            'category_ids.min' => trans('shipping.category_ids_min'),
            'category_ids.*.exists' => trans('shipping.category_id_not_exist'),
            'department_ids.required' => trans('shipping.department_ids_required'),
            'department_ids.min' => trans('shipping.department_ids_min'),
            'department_ids.*.exists' => trans('shipping.department_id_not_exist'),
            'sub_category_ids.required' => trans('shipping.sub_category_ids_required'),
            'sub_category_ids.min' => trans('shipping.sub_category_ids_min'),
            'sub_category_ids.*.exists' => trans('shipping.sub_category_id_not_exist'),
        ];
    }
}
