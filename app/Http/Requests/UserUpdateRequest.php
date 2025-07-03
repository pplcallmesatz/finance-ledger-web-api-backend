<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;
use App\Rules\PhoneNumber;


class UserUpdateRequest extends FormRequest
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
            'email' => [
                'nullable',
                Rule::unique('users', 'email')->ignore($this->user),
                'email',
            ],
            'phone' => [
                'nullable', 
                Rule::unique('users', 'phone')->ignore($this->user) , 
                new PhoneNumber
            ],
            'remarks' => ['nullable', 'max:255', 'string'],
        ];
    }
}
