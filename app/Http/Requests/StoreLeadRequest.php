<?php

namespace App\Http\Requests;

use App\Models\GooglePlaceProspect;
use App\Models\Lead;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreLeadRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();

        if (! $user || ! $user->is_active) {
            return false;
        }

        if ($this->filled('place_prospect_id')) {
            $prospect = GooglePlaceProspect::query()->find($this->input('place_prospect_id'));
            if (! $prospect || (! $user->canManageTeam() && (int) $prospect->assigned_to !== (int) $user->id)) {
                return false;
            }
        }

        return $user->canManageTeam() || ! $this->filled('assigned_to') || (int) $this->input('assigned_to') === (int) $user->id;
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
            'place_prospect_id' => ['nullable', 'integer', 'exists:google_place_prospects,id'],
            'independent_contact_verified' => ['nullable', Rule::requiredIf(fn (): bool => $this->filled('place_prospect_id')), 'accepted'],
        ];
    }

    public function messages(): array
    {
        return [
            'phone.regex' => 'Enter a valid phone number with digits, spaces, or a country code.',
            'independent_contact_verified.required' => 'Confirm that the contact details were independently verified before creating this CRM lead.',
            'independent_contact_verified.accepted' => 'Confirm that the contact details were independently verified before creating this CRM lead.',
        ];
    }
}
