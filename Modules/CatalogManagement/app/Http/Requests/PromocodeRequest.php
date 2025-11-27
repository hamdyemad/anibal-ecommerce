<?php

namespace Modules\CatalogManagement\app\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PromocodeRequest extends FormRequest
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
        // Assuming the route parameter is named 'promocode' or 'id'
        $promocodeId = $this->route('promocode') ?? $this->route('id');

        return [
            'code' => ['required', 'string', 'max:255', Rule::unique('promocodes', 'code')->ignore($promocodeId)],
            'maximum_of_use' => 'required|integer|min:0',
            'type' => 'required|in:percent,amount',
            'value' => 'required|numeric|min:0',
            'valid_from' => 'required|date',
            'valid_until' => 'required|date|after_or_equal:valid_from',
            'dedicated_to' => 'required|in:all,male,female',
            'is_active' => 'nullable|boolean', // Checkbox usually sends '1' or nothing/null
        ];
    }
}
