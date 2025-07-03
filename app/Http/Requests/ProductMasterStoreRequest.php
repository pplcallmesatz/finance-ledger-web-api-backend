<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductMasterStoreRequest extends FormRequest
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
            'category_id' => ['required', 'exists:category_masters,id'],
            'name' => ['required', 'string'],
            'purchase_price' => ['required', 'numeric'],
            'purchase_date' => ['required', 'date'],
            'manufacturing_date' => ['required', 'date'],
            'transportation_cost' => ['required', 'numeric'],
            'invoice_number' => ['required', 'string'],
            'quantity_purchased' => ['required', 'numeric'],
            'vendor' => ['nullable', 'string'],
            'expire_date' => ['required', 'date'],
            'total_piece' =>['nullable', 'string']
        ];
    }
}
