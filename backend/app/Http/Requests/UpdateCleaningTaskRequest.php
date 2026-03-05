<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCleaningTaskRequest extends FormRequest
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
            'assigned_to_user_id' => 'nullable|exists:users,id',
            'priority' => 'sometimes|in:low,medium,high,urgent',
            'description' => 'nullable|string',
            'checklist' => 'nullable|array',
            'scheduled_date' => 'sometimes|date',
            'duration_minutes' => 'nullable|integer|min:1',
            'notes' => 'nullable|string',
            'status' => 'sometimes|in:pending,in_progress,completed,cancelled,verified',
            'completed_at' => 'nullable|date',
            'photos' => 'nullable|array',
        ];
    }
}
