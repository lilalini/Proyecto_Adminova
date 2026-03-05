<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreApartmentChannelRequest extends FormRequest
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
            'distribution_channel_id' => 'required|exists:distribution_channels,id',
            'external_listing_id' => 'nullable|string',
            'external_url' => 'nullable|url',
            'connection_status' => 'sometimes|in:connected,disconnected,error',
            'sync_enabled' => 'boolean',
            'sync_price' => 'boolean',
            'sync_availability' => 'boolean',
            'sync_content' => 'boolean',
            'price_adjustment_type' => 'sometimes|in:percentage,fixed,none',
            'price_adjustment_value' => 'nullable|numeric|min:0',
            'min_stay_adjustment' => 'nullable|integer|min:1',
        ];
    }
}
