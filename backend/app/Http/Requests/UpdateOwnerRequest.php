<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class UpdateOwnerRequest extends FormRequest
{
    public function authorize(): bool
    {
        $owner = $this->route('owner');
        return Gate::allows('update', $owner);
    }

    public function rules(): array
    {
        return [
            'first_name' => 'sometimes|string|max:255',
            'last_name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:owners,email,' . $this->owner?->id,
            'password' => 'sometimes|string|min:8',
            'phone' => 'sometimes|string|max:20',
            'document_type' => 'sometimes|string|in:DNI,NIE,Passport',
            'document_number' => 'sometimes|string|unique:owners,document_number,' . $this->owner?->id,
            'address' => 'sometimes|string',
            'city' => 'sometimes|string|max:255',
            'postal_code' => 'sometimes|string|max:10',
            'country' => 'sometimes|string|size:2',
            'iban' => 'nullable|string',
            'commission_rate' => 'nullable|numeric|min:0|max:100',
            'contract_signed' => 'boolean',
            'contract_date' => 'nullable|date',
            'is_active' => 'boolean',
        ];
    }
}