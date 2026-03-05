<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;


class UpdateGuestPaymentMethodRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
       $paymentMethod = $this->route('guest_payment_method');
        return Gate::allows('update', $paymentMethod);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'method_type' => 'sometimes|in:credit_card,paypal,apple_pay,google_pay',
            'token' => 'nullable|string',
            'card_last_four' => 'nullable|string|size:4',
            'card_brand' => 'nullable|string|in:visa,mastercard,amex',
            'card_expiry_month' => 'nullable|string|size:2',
            'card_expiry_year' => 'nullable|string|size:4',
            'is_default' => 'boolean',
            'is_expired' => 'boolean',
        ];
    }
}
