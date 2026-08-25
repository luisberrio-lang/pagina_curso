<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CheckoutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email:rfc', 'max:255'],
            'phone' => ['required', 'string', 'max:30', 'regex:/^[0-9+()\s-]{7,30}$/'],
            'document_type' => ['nullable', 'required_with:document_number', Rule::in(['DNI', 'CE', 'RUC', 'PASAPORTE'])],
            'document_number' => ['nullable', 'required_with:document_type', 'string', 'max:30', 'regex:/^[A-Za-z0-9-]+$/'],
            'checkout_token' => ['required', 'uuid'],
        ];
    }
}
