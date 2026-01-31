<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => [
                'required',
                'email:rfc',
                'max:255',
            ],
            'password' => [
                'required',
                'string',
                'max:128', // Prevent long password DoS
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => 'Email address is required.',
            'email.email' => 'Please enter a valid email address.',
            'email.max' => 'Email address is too long.',
            'password.required' => 'Password is required.',
            'password.max' => 'Password is too long.',
        ];
    }

    /**
     * Prepare the data for validation.
     * Sanitize inputs before validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'email' => strtolower(trim($this->email ?? '')),
        ]);
    }
}
