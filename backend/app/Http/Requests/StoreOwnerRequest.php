<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use App\Models\Owner;

class StoreOwnerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Gate::allows('create', Owner::class);
    }

    public function rules(): array
    {
        return [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:owners,email',
            'password' => 'required|string|min:8',
            'phone' => 'required|string|max:20',
            'document_type' => 'required|string|in:DNI,NIE,Passport',
            'document_number' => 'required|string|unique:owners,document_number',
            'address' => 'required|string',
            'city' => 'required|string|max:255',
            'postal_code' => 'required|string|max:10',
            'country' => 'required|string|size:2',
            'iban' => 'nullable|string',
            'commission_rate' => 'nullable|numeric|min:0|max:100',
            'contract_signed' => 'boolean',
            'contract_date' => 'nullable|date',
            'is_active' => 'boolean',
        ];
    }
}