<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TransactionUpdateRequest extends FormRequest
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
            'bank_balance' => ['required', 'numeric'],
            'cash_in_hand' => ['required', 'numeric'],
            'expense_ledger_id' => ['nullable', 'exists:expense_ledger,id'],
            'reason' => ['nullable', 'max:255', 'string'],
        ];
    }
}
