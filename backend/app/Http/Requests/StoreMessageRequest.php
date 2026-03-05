<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMessageRequest extends FormRequest
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
            'receiver_type' => 'required|in:App\Models\User,App\Models\Owner,App\Models\Guest',
            'receiver_id' => 'required|integer',
            'accommodation_id' => 'nullable|exists:accommodations,id',
            'booking_id' => 'nullable|exists:bookings,id',
            'parent_id' => 'nullable|exists:messages,id',
            'subject' => 'required|string|max:255',
            'body' => 'required|string',
            'message_type' => 'required|in:general,question,complaint,reservation',
            'priority' => 'required|in:low,normal,high',
        ];
    }
}
