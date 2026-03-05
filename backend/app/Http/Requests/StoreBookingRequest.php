<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBookingRequest extends FormRequest
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
            'accommodation_id' => 'required|exists:accommodations,id',
            'guest_id' => 'required|exists:guests,id',
            'check_in' => 'required|date',
            'check_out' => 'required|date|after:check_in',
            'nights' => 'required|integer|min:1',
            'adults' => 'required|integer|min:1',
            'children' => 'nullable|integer|min:0',
            'infants' => 'nullable|integer|min:0',
            'pets' => 'nullable|integer|min:0',
            'price_per_night' => 'required|numeric|min:0',
            'base_price' => 'required|numeric|min:0',
            'total_amount' => 'required|numeric|min:0',
            'balance_due' => 'required|numeric|min:0',
            'guest_name' => 'required|string|max:255',
            'guest_email' => 'nullable|email',
            'guest_phone' => 'nullable|string',
            'source' => 'nullable|string',
            'status' => 'nullable|string',
            'currency' => 'nullable|string|size:3',
            'payment_status' => 'nullable|string',
        ];
    }
}
