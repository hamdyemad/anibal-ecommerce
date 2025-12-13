<?php

namespace Modules\CatalogManagement\app\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class FilterTypeRequest extends FormRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'type' => 'required|in:occasion,bundle',
        ];
    }

}
