<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ExpenseLedgerStoreRequest extends FormRequest
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
            'name' => ['required', 'max:255', 'string'],
            'description' => ['required', 'max:255', 'string'],
            'invoice_number' => ['required', 'max:255', 'string'],
            'purchase_price' => ['required', 'numeric'],
            'seller' => ['required', 'max:255', 'string'],
            'purchase_date' => ['required', 'date'],
            'payment_method' => ['required', 'max:255', 'string'],
            'expense_type' => ['required', 'max:255', 'string'],
            'deduct' => ['nullable', 'max:255', 'string']
        ];
    }
}
