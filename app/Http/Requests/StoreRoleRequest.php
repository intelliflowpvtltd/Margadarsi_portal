<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreRoleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'company_id' => 'required|exists:companies,id',
            'name' => 'required|string|max:100',
            'slug' => [
                'nullable',
                'string',
                'max:100',
                'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
                Rule::unique('roles')->where(function ($query) {
                    return $query->where('company_id', $this->input('company_id'));
                }),
            ],
            'description' => 'nullable|string|max:500',
            'hierarchy_level' => 'required|integer|between:1,99',
            'is_active' => 'boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'slug.regex' => 'Slug must contain only lowercase letters, numbers, and hyphens.',
            'slug.unique' => 'This role slug already exists for this company.',
            'hierarchy_level.between' => 'Hierarchy level must be between 1 (highest) and 99 (lowest).',
        ];
    }
}
