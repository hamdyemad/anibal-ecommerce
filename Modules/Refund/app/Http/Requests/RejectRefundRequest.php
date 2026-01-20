<?php

namespace Modules\Refund\app\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RejectRefundRequest extends FormRequest
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
            'cancellation_reason' => 'required|string|max:1000',
            'rejection_reason' => 'nullable|string|max:1000',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // If rejection_reason is provided but cancellation_reason is not, use rejection_reason
        if ($this->has('rejection_reason') && !$this->has('cancellation_reason')) {
            $this->merge([
                'cancellation_reason' => $this->input('rejection_reason'),
            ]);
        }
    }

    /**
     * Get custom attribute names for validator errors.
     */
    public function attributes(): array
    {
        return [
            'cancellation_reason' => trans('refund::refund.fields.cancellation_reason'),
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'cancellation_reason.required' => trans('refund::refund.validation.cancellation_reason_required'),
            'cancellation_reason.max' => trans('validation.max.string', ['attribute' => trans('refund::refund.fields.cancellation_reason'), 'max' => 1000]),
        ];
    }
}
