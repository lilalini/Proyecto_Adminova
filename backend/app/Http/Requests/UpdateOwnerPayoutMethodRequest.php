<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class UpdateOwnerPayoutMethodRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $payoutMethod = $this->route('owner_payout_method');
        return Gate::allows('update', $payoutMethod);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'method_type' => 'sometimes|in:bank_transfer,paypal,wise',
            'account_holder' => 'sometimes|string|max:255',
            'account_number' => 'sometimes|string|max:255',
            'bank_name' => 'nullable|string|max:255',
            'bank_swift' => 'nullable|string|max:50',
            'is_default' => 'boolean',
            'is_verified' => 'sometimes|boolean',
            'notes' => 'nullable|string',
        ];
    }
}
