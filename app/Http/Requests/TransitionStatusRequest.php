<?php

namespace App\Http\Requests;

use App\Models\Lead;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TransitionStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status'            => ['required', 'string', Rule::in(array_keys(Lead::STATUSES))],
            'reason'            => 'nullable|string|max:500',
            'nq_reason_id'      => 'nullable|exists:nq_reasons,id',
            'closure_reason_id' => 'nullable|exists:closure_reasons,id',
        ];
    }

    public function messages(): array
    {
        return [
            'status.required' => 'Target status is required.',
            'status.in'       => 'Invalid target status.',
        ];
    }
}
