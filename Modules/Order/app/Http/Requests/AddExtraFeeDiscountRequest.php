<?php

namespace Modules\Order\app\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddExtraFeeDiscountRequest extends FormRequest
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
            'cost' => 'required|numeric|min:0',
            'reason' => 'required|string|max:255',
            'type' => 'required|in:discount,fee',
        ];
    }
}
