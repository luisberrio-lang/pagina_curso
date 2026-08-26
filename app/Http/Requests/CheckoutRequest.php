<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CheckoutRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        $this->merge([
            'full_name' => trim((string) $this->input('full_name')),
            'email' => mb_strtolower(trim((string) $this->input('email'))),
            'phone' => trim((string) $this->input('phone')),
        ]);
    }

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'full_name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email:rfc', 'max:255'],
            'phone' => ['required', 'string', 'min:7', 'max:30', 'regex:/^[\p{N}+()\.\s-]+$/u'],
            'checkout_token' => ['required', 'uuid'],
            'terms_accepted' => ['accepted'],
        ];
    }
}
