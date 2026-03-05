<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class UpdatePaymentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $payment = $this->route('payment');
        return Gate::allows('update', $payment);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'payment_type' => 'sometimes|in:deposit,final,full,damage_deposit',
            'method' => 'sometimes|in:credit_card,transfer,cash,paypal,stripe,other',
            'amount' => 'sometimes|numeric|min:0.01',
            'status' => 'sometimes|in:pending,completed,failed,refunded',
            'payment_date' => 'nullable|date',
            'due_date' => 'nullable|date',
            'notes' => 'nullable|string',
            'transaction_id' => 'nullable|string',
            'receipt_sent' => 'sometimes|boolean',
            'refund_reason' => 'nullable|string|required_if:status,refunded',
        ];
    }
}
