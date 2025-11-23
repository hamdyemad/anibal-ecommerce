<?php

namespace Modules\Customer\app\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class CreateAddressRequest extends FormRequest
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
            'title' => 'required|string|max:255',
            'address' => 'required|string|max:500',
            'postal_code' => 'nullable|string|max:20',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'country_id' => 'required|integer|exists:countries,id',
            'city_id' => 'required|integer|exists:cities,id',
            'region_id' => 'required|integer|exists:regions,id',
            'subregion_id' => 'nullable|integer|exists:subregions,id',
            'is_primary' => 'sometimes|boolean',
        ];
    }
}
