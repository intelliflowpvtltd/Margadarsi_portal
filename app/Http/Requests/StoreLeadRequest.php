<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreLeadRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Handled by middleware
    }

    public function rules(): array
    {
        return [
            'company_id'        => 'required|exists:companies,id',
            'project_id'        => 'required|exists:projects,id',
            'name'              => 'required|string|max:150',
            'mobile'            => 'required|string|max:15',
            'alt_mobile'        => 'nullable|string|max:15',
            'whatsapp'          => 'nullable|string|max:15',
            'email'             => 'nullable|email|max:150',
            'city'              => 'nullable|string|max:100',
            'state'             => 'nullable|string|max:100',
            'pincode'           => 'nullable|string|max:10',
            'address'           => 'nullable|string',
            'lead_source_id'    => 'nullable|exists:lead_sources,id',
            'temperature_tag_id'=> 'nullable|exists:temperature_tags,id',
            'budget_range_id'   => 'nullable|exists:budget_ranges,id',
            'property_type_id'  => 'nullable|exists:property_types,id',
            'timeline_id'       => 'nullable|exists:timelines,id',
            'requirements_notes'=> 'nullable|string',
            'source_campaign'   => 'nullable|string|max:150',
            'source_medium'     => 'nullable|string|max:100',
            'utm_source'        => 'nullable|string|max:100',
            'utm_medium'        => 'nullable|string|max:100',
            'utm_campaign'      => 'nullable|string|max:150',
        ];
    }

    public function messages(): array
    {
        return [
            'company_id.required' => 'Company is required.',
            'project_id.required' => 'Project is required.',
            'name.required'       => 'Lead name is required.',
            'mobile.required'     => 'Mobile number is required.',
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('mobile')) {
            $this->merge(['mobile' => \App\Models\Lead::normalizePhone($this->mobile)]);
        }
        if ($this->has('alt_mobile') && $this->alt_mobile) {
            $this->merge(['alt_mobile' => \App\Models\Lead::normalizePhone($this->alt_mobile)]);
        }
        if ($this->has('whatsapp') && $this->whatsapp) {
            $this->merge(['whatsapp' => \App\Models\Lead::normalizePhone($this->whatsapp)]);
        }
    }
}
