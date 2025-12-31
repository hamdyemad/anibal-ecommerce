<?php

namespace Modules\Order\app\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RespondQuotationOfferRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'action' => ['required', Rule::in(['accept', 'reject'])],
        ];
    }

    public function messages(): array
    {
        return [
            'action.required' => config('responses.action_required')[app()->getLocale()],
            'action.in' => config('responses.action_invalid')[app()->getLocale()],
        ];
    }
}
