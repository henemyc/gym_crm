<?php

namespace App\Http\Requests;

use App\Models\FollowUp;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreFollowUpRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) $this->user()?->is_active;
    }

    public function rules(): array
    {
        return [
            'lead_id' => ['required', 'integer', Rule::exists('leads', 'id')->whereNull('deleted_at')],
            'kind' => ['required', Rule::in(array_keys(FollowUp::KINDS))],
            'due_at' => ['required', 'date'],
            'assigned_to' => ['nullable', 'integer', Rule::exists('users', 'id')->where('is_active', true)],
            'meeting_at' => ['nullable', 'date'],
            'meeting_with' => ['nullable', 'string', 'max:120', 'required_with:meeting_at', 'required_if:kind,meeting'],
            'trial_at' => ['nullable', 'date'],
            'trial_details' => ['nullable', 'string', 'max:2000'],
            'remark' => ['nullable', 'string', 'max:5000'],
        ];
    }
}
