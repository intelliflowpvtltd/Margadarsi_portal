<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
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
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $user = $this->route('user');

        return [
            'role_id' => [
                'sometimes',
                'exists:roles,id',
                // Ensure role belongs to the same company as the user
                Rule::exists('roles', 'id')->where('company_id', $user->company_id),
            ],
            'first_name' => ['sometimes', 'string', 'max:100'],
            'last_name' => ['sometimes', 'string', 'max:100'],
            'email' => [
                'sometimes',
                'email',
                'max:255',
                // Email must be unique per company, excluding current user
                Rule::unique('users', 'email')
                    ->where('company_id', $user->company_id)
                    ->ignore($user->id),
            ],
            'password' => ['sometimes', 'string', 'min:8', 'confirmed'],
            'phone' => ['sometimes', 'nullable', 'string', 'max:20', 'regex:/^[0-9\s\-\+\(\)]+$/'],
            'avatar' => ['sometimes', 'nullable', 'string', 'max:255'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'role_id.exists' => 'The selected role must belong to the same company.',
            'email.unique' => 'This email is already registered for this company.',
        ];
    }
}
