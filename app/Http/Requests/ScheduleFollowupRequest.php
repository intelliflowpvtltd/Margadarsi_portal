<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ScheduleFollowupRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'followup_at' => 'required|date|after:now',
            'notes'       => 'nullable|string|max:1000',
        ];
    }

    public function messages(): array
    {
        return [
            'followup_at.required' => 'Follow-up date/time is required.',
            'followup_at.after'    => 'Follow-up must be a future date.',
        ];
    }
}
