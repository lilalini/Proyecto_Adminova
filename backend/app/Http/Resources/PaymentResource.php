<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'payment_reference' => $this->payment_reference,
            'booking' => new BookingResource($this->whenLoaded('booking')),
            'guest' => new GuestResource($this->whenLoaded('guest')),
            'user' => new UserResource($this->whenLoaded('user')),
            'payment_type' => $this->payment_type,
            'method' => $this->method,
            'amount' => (float) $this->amount,
            'currency' => $this->currency,
            'status' => $this->status,
            'transaction_id' => $this->transaction_id,
            'payment_date' => $this->payment_date,
            'due_date' => $this->due_date,
            'notes' => $this->notes,
            'receipt_sent' => $this->receipt_sent,
            'receipt_sent_at' => $this->receipt_sent_at,
            'refunded_at' => $this->refunded_at,
            'refund_reason' => $this->refund_reason,
            'created_at' => $this->created_at,
        ];
    }
}
