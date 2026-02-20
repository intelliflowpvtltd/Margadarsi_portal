<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MarkLostLeadRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'closure_reason_id' => 'required|exists:closure_reasons,id',
            'notes'             => 'nullable|string|max:1000',
        ];
    }

    public function messages(): array
    {
        return [
            'closure_reason_id.required' => 'A closure reason is required.',
        ];
    }
}
