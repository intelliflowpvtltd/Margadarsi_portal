<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Rules\RoleAuthorityCheck;

class StoreUserRequest extends FormRequest
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
        return [
            'company_id' => ['required', 'exists:companies,id'],
            'role_id' => [
                'required',
                'exists:roles,id',
                // Ensure role belongs to the same company
                Rule::exists('roles', 'id')->where('company_id', $this->input('company_id')),
                // Ensure user can only assign roles at or below their authority level
                new RoleAuthorityCheck(auth()->user()->role),
            ],
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'email' => [
                'required',
                'email',
                'max:255',
                // Email must be unique per company
                Rule::unique('users', 'email')->where('company_id', $this->input('company_id')),
            ],
            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/',
            ],
            'phone' => ['nullable', 'string', 'max:20', 'regex:/^[0-9\s\-\+\(\)]+$/'],
            'avatar' => ['nullable', 'string', 'max:255'],
            'is_active' => ['sometimes', 'boolean'],

            // Optional project assignments
            'project_ids' => ['sometimes', 'array'],
            'project_ids.*' => [
                'exists:projects,id',
                // Ensure projects belong to the same company
                Rule::exists('projects', 'id')->where('company_id', $this->input('company_id')),
            ],
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
            'project_ids.*.exists' => 'One or more projects do not belong to the same company.',
            'password.regex' => 'Password must contain at least one uppercase letter, one lowercase letter, one digit, and one special character (@$!%*?&).',
        ];
    }
}
