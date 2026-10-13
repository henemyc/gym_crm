<?php

namespace App\Http\Requests;

use App\Models\Lead;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateLeadRequest extends FormRequest
{
    public function authorize(): bool
    {
        $lead = $this->route('lead');
        return $this->user() && $lead instanceof Lead && $this->user()->can('update', $lead)
            && ($this->user()->canManageTeam() || ! $this->filled('assigned_to') || (int) $this->input('assigned_to') === $lead->assigned_to);
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:120'],
            'gym_name' => ['required', 'string', 'max:160'],
            'phone' => ['required', 'string', 'max:32', 'regex:/^[0-9+().\-\s]{8,32}$/'],
            'location' => ['nullable', 'string', 'max:120'],
            'source' => ['required', Rule::in(Lead::SOURCES)],
            'priority' => ['required', Rule::in(array_keys(Lead::PRIORITIES))],
            'status' => ['required', Rule::in(array_keys(Lead::STATUSES))],
            'notes' => ['nullable', 'string', 'max:10000'],
            'assigned_to' => ['nullable', 'integer', Rule::exists('users', 'id')->where('is_active', true)],
        ];
    }

    public function messages(): array
    {
        return ['phone.regex' => 'Enter a valid phone number with digits, spaces, or a country code.'];
    }
}
