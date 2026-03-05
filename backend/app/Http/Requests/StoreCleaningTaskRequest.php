<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCleaningTaskRequest extends FormRequest
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
            'accommodation_id' => 'required|exists:accommodations,id',
            'booking_id' => 'nullable|exists:bookings,id',
            'assigned_to_user_id' => 'nullable|exists:users,id',
            'task_type' => 'required|in:cleaning,maintenance,inspection,laundry',
            'priority' => 'required|in:low,medium,high,urgent',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'checklist' => 'nullable|array',
            'scheduled_date' => 'required|date',
            'duration_minutes' => 'nullable|integer|min:1',
            'notes' => 'nullable|string',
        ];
    }
}
