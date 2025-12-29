<?php

namespace Modules\CatalogManagement\app\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class VariantByKeyRequest extends FormRequest
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
            'parent_id' => 'nullable|integer|exists:variants_configurations,id',
        ];
    }
}
