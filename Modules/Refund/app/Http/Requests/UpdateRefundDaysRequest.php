<?php

namespace Modules\Refund\app\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRefundDaysRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return isAdmin();
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'refund_processing_days' => 'required|integer|min:1|max:365',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'refund_processing_days.required' => trans('refund::refund.validation.refund_processing_days_required'),
            'refund_processing_days.integer' => trans('refund::refund.validation.refund_processing_days_integer'),
            'refund_processing_days.min' => trans('refund::refund.validation.refund_processing_days_min', ['min' => 1]),
            'refund_processing_days.max' => trans('refund::refund.validation.refund_processing_days_max', ['max' => 365]),
        ];
    }
}
