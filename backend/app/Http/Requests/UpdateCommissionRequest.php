<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class UpdateCommissionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $commission = $this->route('commission');
        return Gate::allows('update', $commission);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'rate' => 'sometimes|numeric|min:0|max:100',
            'amount' => 'sometimes|numeric|min:0',
            'status' => 'sometimes|in:pending,calculated,invoiced,paid',
            'invoice_number' => 'nullable|string',
            'invoice_date' => 'nullable|date',
            'due_date' => 'nullable|date',
            'paid_at' => 'nullable|date',
            'notes' => 'nullable|string',
        ];
    }
}
