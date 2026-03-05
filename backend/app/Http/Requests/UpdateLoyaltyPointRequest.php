<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class UpdateLoyaltyPointRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $point = $this->route('loyalty_point');
        return Gate::allows('update', $point);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'points' => 'sometimes|integer',
            'description' => 'nullable|string',
            'expiry_date' => 'nullable|date',
            'type' => 'sometimes|in:earned,redeemed,expired,adjusted',
        ];
    }
}
