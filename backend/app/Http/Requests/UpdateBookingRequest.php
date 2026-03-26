<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBookingRequest extends FormRequest
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
            'check_in' => 'sometimes|date',
            'check_out' => 'sometimes|date|after:check_in',
            'nights' => 'sometimes|integer|min:1',
            'adults' => 'sometimes|integer|min:1',
            'children' => 'nullable|integer|min:0',
            'infants' => 'nullable|integer|min:0',
            'pets' => 'nullable|integer|min:0',
            'status' => 'sometimes|in:pending,confirmed,checked_in,checked_out,cancelled,no_show',
            'guest_name' => 'sometimes|string|max:255',
            'guest_email' => 'nullable|email',
            'guest_phone' => 'nullable|string',
            'guest_notes' => 'nullable|string',
            'staff_notes' => 'nullable|string',
        ];
    }
}
