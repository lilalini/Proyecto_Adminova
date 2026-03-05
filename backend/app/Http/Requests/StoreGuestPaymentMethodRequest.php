<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use App\Models\GuestPaymentMethod;

class StoreGuestPaymentMethodRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Gate::allows('create', GuestPaymentMethod::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'guest_id' => 'required|exists:guests,id',
            'method_type' => 'required|in:credit_card,paypal,apple_pay,google_pay',
            'token' => 'nullable|string',
            'card_last_four' => 'required_if:method_type,credit_card|nullable|string|size:4',
            'card_brand' => 'required_if:method_type,credit_card|nullable|string|in:visa,mastercard,amex',
            'card_expiry_month' => 'required_if:method_type,credit_card|nullable|string|size:2',
            'card_expiry_year' => 'required_if:method_type,credit_card|nullable|string|size:4',
            'is_default' => 'boolean',
        ];
    }
}
