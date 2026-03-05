<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDocumentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
       return true; // Policy en controlador
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'documentable_type' => 'required|string|in:App\Models\User,App\Models\Owner,App\Models\Guest,App\Models\Accommodation',
            'documentable_id' => 'required|integer',
            'document_type' => 'required|string|max:100',
            'title' => 'required|string|max:255',
            'file' => 'required|file|mimes:pdf,jpg,jpeg,png|max:10240', // 10MB max
            'valid_from' => 'nullable|date',
            'valid_until' => 'nullable|date|after:valid_from',
            'notes' => 'nullable|string',
        ];
    }
}
