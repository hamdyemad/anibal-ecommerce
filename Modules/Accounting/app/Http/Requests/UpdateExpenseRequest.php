<?php

namespace Modules\Accounting\app\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateExpenseRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'expense_item_id' => 'nullable|exists:expense_items,id',
            'amount' => 'required|numeric|min:0',
            'description' => 'required|string|max:500',
            'expense_date' => 'required|date',
            'receipt_file' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048'
        ];
    }
}

