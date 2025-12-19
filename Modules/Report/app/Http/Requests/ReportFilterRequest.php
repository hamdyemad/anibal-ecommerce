<?php

namespace Modules\Report\app\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ReportFilterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'from' => 'nullable|date_format:Y-m-d',
            'to' => 'nullable|date_format:Y-m-d',
            'from_date' => 'nullable|date_format:Y-m-d',
            'to_date' => 'nullable|date_format:Y-m-d',
            'search' => 'nullable|string|max:500',
            'status' => 'nullable|string|in:active,inactive',
            'gender' => 'nullable|string|in:male,female,other',
            'type' => 'nullable|string|max:255',
            'category' => 'nullable|integer',
            'vendor' => 'nullable|integer',
            'page' => 'nullable|integer|min:1',
            'per_page' => 'nullable|integer|min:1|max:100',
        ];
    }

    public function messages(): array
    {
        return [
            'to.after_or_equal' => 'The end date must be after or equal to the start date.',
            'category.exists' => 'The selected category does not exist.',
            'vendor.exists' => 'The selected vendor does not exist.',
        ];
    }

    protected function prepareForValidation()
    {
        // Map from_date/to_date to from/to for consistency
        $this->merge([
            'from' => $this->input('from_date') ?? $this->input('from'),
            'to' => $this->input('to_date') ?? $this->input('to'),
            'page' => $this->input('page', 1),
            'per_page' => $this->input('per_page', 15),
        ]);
    }
}
