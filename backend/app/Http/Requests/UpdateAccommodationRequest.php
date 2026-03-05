<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAccommodationRequest extends FormRequest
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
            'title' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'property_type' => 'sometimes|string|max:100',
            'bedrooms' => 'sometimes|integer|min:0',
            'bathrooms' => 'sometimes|integer|min:0',
            'max_guests' => 'sometimes|integer|min:1',
            'size_m2' => 'nullable|integer|min:1',
            'address' => 'sometimes|string',
            'city' => 'sometimes|string|max:100',
            'postal_code' => 'sometimes|string|max:10',
            'country' => 'sometimes|string|size:2',
            'base_price' => 'sometimes|numeric|min:0',
            'cleaning_fee' => 'nullable|numeric|min:0',
            'security_deposit' => 'nullable|numeric|min:0',
            'minimum_stay' => 'sometimes|integer|min:1',
            'maximum_stay' => 'nullable|integer|min:1',
            'check_in_time' => 'sometimes|string',
            'check_out_time' => 'sometimes|string',
            'status' => 'sometimes|in:draft,published,maintenance,inactive',
            'cancellation_policy_id' => 'sometimes|exists:cancellation_policies,id',
        ];
    }
}
