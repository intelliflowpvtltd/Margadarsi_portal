<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateLeadRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'              => 'sometimes|string|max:150',
            'mobile'            => 'sometimes|string|max:15',
            'alt_mobile'        => 'nullable|string|max:15',
            'whatsapp'          => 'nullable|string|max:15',
            'email'             => 'nullable|email|max:150',
            'city'              => 'nullable|string|max:100',
            'state'             => 'nullable|string|max:100',
            'pincode'           => 'nullable|string|max:10',
            'address'           => 'nullable|string',
            'temperature_tag_id'=> 'nullable|exists:temperature_tags,id',
            'budget_range_id'   => 'nullable|exists:budget_ranges,id',
            'property_type_id'  => 'nullable|exists:property_types,id',
            'timeline_id'       => 'nullable|exists:timelines,id',
            'requirements_notes'=> 'nullable|string',
            'budget_confirmed'  => 'nullable|boolean',
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('mobile') && $this->mobile) {
            $this->merge(['mobile' => \App\Models\Lead::normalizePhone($this->mobile)]);
        }
    }
}
