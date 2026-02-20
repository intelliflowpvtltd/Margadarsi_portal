<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DisqualifyLeadRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nq_reason_id' => 'required|exists:nq_reasons,id',
            'notes'        => 'nullable|string|max:1000',
        ];
    }

    public function messages(): array
    {
        return [
            'nq_reason_id.required' => 'A disqualification reason is required.',
        ];
    }
}
