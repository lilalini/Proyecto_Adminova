<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateGuestByUserIdRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
   public function rules(): array
    {
        return [
            'first_name' => 'sometimes|string|max:255',
            'last_name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email',
            'phone' => 'sometimes|string|max:20',
            'document_type' => 'sometimes|string|in:DNI,NIE,Passport',
            'document_number' => 'sometimes|string|max:50',
            'nationality' => 'sometimes|string|size:2',
            'birth_date' => 'nullable|date|before:today',
            'gender' => 'nullable|string|in:male,female,other',
            'address' => 'sometimes|string|max:255',
            'city' => 'sometimes|string|max:100',
            'postal_code' => 'nullable|string|max:10',
            'country' => 'sometimes|string|size:2',
            'accepts_newsletter' => 'sometimes|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'nationality.size' => 'La nacionalidad debe ser el código ISO del país (2 letras, ej: ES)',
            'birth_date.before' => 'La fecha de nacimiento no puede ser posterior a hoy',
        ];
    }
}
