<?php

namespace Modules\Accounting\app\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BalancesRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'search' => 'nullable|string|max:255',
            'min_balance' => 'nullable|numeric|min:0'
        ];
    }
}

