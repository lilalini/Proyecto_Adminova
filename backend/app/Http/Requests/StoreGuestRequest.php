<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use App\Models\Guest;

class StoreGuestRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Gate::allows('create', Guest::class);
    }

    public function rules(): array
    {
        return [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:guests,email',
            'phone' => 'nullable|string|max:20',
            'document_type' => 'nullable|string|in:DNI,NIE,Passport',
            'document_number' => 'nullable|string|unique:guests,document_number',
            'nationality' => 'nullable|string|size:2',
            'birth_date' => 'nullable|date',
            'gender' => 'nullable|string|in:male,female,other',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:255',
            'postal_code' => 'nullable|string|max:10',
            'country' => 'nullable|string|size:2',
            'source' => 'sometimes|string',
            'external_id' => 'nullable|string|unique:guests,external_id',
        ];
    }
}