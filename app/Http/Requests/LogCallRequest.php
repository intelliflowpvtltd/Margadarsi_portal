<?php

namespace App\Http\Requests;

use App\Models\LeadCall;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class LogCallRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'attempt_outcome'    => ['required', 'string', Rule::in(array_keys(LeadCall::ATTEMPT_OUTCOMES))],
            'call_outcome'       => ['nullable', 'string', Rule::in(array_keys(LeadCall::CALL_OUTCOMES))],
            'duration_seconds'   => 'nullable|integer|min:0',
            'started_at'         => 'nullable|date',
            'ended_at'           => 'nullable|date|after_or_equal:started_at',
            'summary'            => 'nullable|string|max:2000',
            'recording_url'      => 'nullable|url',
            'temperature_tag_id' => 'nullable|exists:temperature_tags,id',
            'next_followup_at'   => 'nullable|date|after:now',
            'action_items'       => 'nullable|string|max:1000',
            'nq_reason_id'       => 'nullable|exists:nq_reasons,id',
            'retry_scheduled_at' => 'nullable|date|after:now',
        ];
    }

    public function messages(): array
    {
        return [
            'attempt_outcome.required' => 'Call attempt outcome is required.',
            'attempt_outcome.in'       => 'Invalid attempt outcome value.',
            'call_outcome.in'          => 'Invalid call outcome value.',
        ];
    }
}
