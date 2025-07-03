<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductUpdateRequest extends FormRequest
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
            'category_master_id' => ['required', 'exists:category_masters,id'],
            'name' => ['required', 'string'],
            'purchase_price' => ['required', 'numeric'],
            'packing_price' => ['required', 'numeric'],
            'selling_price' => ['required', 'numeric'],
            'description' => ['nullable', 'string'],
            'barcode' => ['nullable', 'string'],
            'barcode_vendor' => ['nullable', 'string'],
            'units' => ['nullable', 'string']
        ];
    }
}







