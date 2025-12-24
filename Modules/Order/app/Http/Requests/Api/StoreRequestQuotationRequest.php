<?php

namespace Modules\Order\app\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequestQuotationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:50',
            'address' => 'required|string|max:1000',
            'notes' => 'nullable|string|max:2000',
            'file' => 'required|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => __('validation.required', ['attribute' => __('order::request-quotation.name')]),
            'email.required' => __('validation.required', ['attribute' => __('order::request-quotation.email')]),
            'email.email' => __('validation.email', ['attribute' => __('order::request-quotation.email')]),
            'phone.required' => __('validation.required', ['attribute' => __('order::request-quotation.phone')]),
            'address.required' => __('validation.required', ['attribute' => __('order::request-quotation.address')]),
            'file.mimes' => __('validation.mimes', ['attribute' => __('order::request-quotation.file'), 'values' => 'pdf, doc, docx, jpg, jpeg, png']),
            'file.max' => __('validation.max.file', ['attribute' => __('order::request-quotation.file'), 'max' => '10MB']),
        ];
    }
}
