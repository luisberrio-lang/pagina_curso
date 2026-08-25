<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CheckoutRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        $this->merge([
            'first_name' => trim((string) $this->input('first_name')),
            'last_name' => trim((string) $this->input('last_name')),
            'email' => mb_strtolower(trim((string) $this->input('email'))),
            'phone' => trim((string) $this->input('phone')),
            'document_type' => $this->filled('document_type') ? trim((string) $this->input('document_type')) : null,
            'document_number' => $this->filled('document_number') ? trim((string) $this->input('document_number')) : null,
        ]);
    }

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
            'terms_accepted' => ['accepted'],
        ];
    }
}
