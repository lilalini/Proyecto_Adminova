<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use App\Models\AvailabilityCalendar;

class StoreAvailabilityRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
         //return Gate::allows('create', AvailabilityCalendar::class);
         return true; // La policy se aplica en el controlador
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
            'date' => 'required|date|after:today|before_or_equal:' . now()->addMonths(12)->format('Y-m-d'),
            'status' => 'required|in:available,booked,blocked,maintenance',
            'price' => 'nullable|numeric|min:0',
            'min_nights' => 'nullable|integer|min:1',
            'max_nights' => 'nullable|integer|min:1',
            'closed_to_arrival' => 'boolean',
            'closed_to_departure' => 'boolean',
            'notes' => 'nullable|string',
        ];
    }
}
