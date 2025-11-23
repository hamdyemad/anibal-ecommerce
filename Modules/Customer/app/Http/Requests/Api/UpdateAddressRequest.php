<?php

namespace Modules\Customer\app\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAddressRequest extends FormRequest
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
            'title' => 'sometimes|string|max:255',
            'address' => 'sometimes|string|max:500',
            'postal_code' => 'sometimes|string|max:20',
            'latitude' => 'sometimes|numeric|between:-90,90',
            'longitude' => 'sometimes|numeric|between:-180,180',
            'country_id' => 'sometimes|integer|exists:countries,id',
            'city_id' => 'sometimes|integer|exists:cities,id',
            'region_id' => 'sometimes|integer|exists:regions,id',
            'subregion_id' => 'sometimes|integer|exists:subregions,id',
            'is_primary' => 'sometimes|boolean',
        ];
    }
}
