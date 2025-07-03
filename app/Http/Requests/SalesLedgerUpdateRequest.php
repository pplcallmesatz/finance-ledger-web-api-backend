<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SalesLedgerUpdateRequest extends FormRequest
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
            'user_id' => ['required', 'exists:users,id'],
            'payment_status' => ['required', 'max:255', 'string'],
            'remarks' => ['nullable', 'max:255', 'string'],
            'company_address' => ['nullable', 'max:255', 'string'],
            'sales_date' => ['nullable', 'date'],
            'invoice_number' => ['nullable', 'max:255', 'string'],
            'payment_method' => ['required', 'max:255', 'string'],
            'products' => ['array']
        ];
    }
}
