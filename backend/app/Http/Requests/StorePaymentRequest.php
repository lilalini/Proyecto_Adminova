<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use App\Models\Payment;

class StorePaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Gate::allows('create', Payment::class);
    }

    public function rules(): array
    {
        return [
            'booking_id' => 'required|exists:bookings,id',
            'guest_id' => 'nullable|exists:guests,id',
            'payment_type' => 'required|in:deposit,final,full,damage_deposit',
            'method' => 'required|in:credit_card,transfer,cash,paypal,stripe,other',
            'amount' => 'required|numeric|min:0.01',
            'currency' => 'required|string|size:3',
            'status' => 'required|in:pending,completed,failed,refunded',
            'payment_date' => 'nullable|date',
            'due_date' => 'nullable|date',
            'notes' => 'nullable|string',
            'transaction_id' => 'nullable|string',
        ];
    }
}