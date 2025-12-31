<?php

namespace Modules\Order\app\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreRequestQuotationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'address_id' => ['required', Rule::exists('customer_addresses', 'id')
                ->where('customer_id', auth('sanctum')->id())],
            'notes' => 'required|string|max:2000',
            'file' => 'required|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240',
        ];
    }

    public function messages(): array
    {
        return [
            'address_id.required' => __('validation.required', ['attribute' => __('order::request-quotation.address')]),
            'address_id.exists' => __('validation.exists', ['attribute' => __('order::request-quotation.address')]),
            'notes.required' => __('validation.required', ['attribute' => __('order::request-quotation.notes')]),
            'notes.max' => __('validation.max.string', ['attribute' => __('order::request-quotation.notes'), 'max' => 2000]),
            'file.required' => __('validation.required', ['attribute' => __('order::request-quotation.file')]),
            'file.mimes' => __('validation.mimes', ['attribute' => __('order::request-quotation.file'), 'values' => 'pdf, doc, docx, jpg, jpeg, png']),
            'file.max' => __('validation.max.file', ['attribute' => __('order::request-quotation.file'), 'max' => '10MB']),
        ];
    }
}
