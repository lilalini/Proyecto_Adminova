<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CommissionResource extends JsonResource
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
            'booking' => new BookingResource($this->whenLoaded('booking')),
            'channel' => new DistributionChannelResource($this->whenLoaded('channel')),
            'accommodation' => new AccommodationResource($this->whenLoaded('accommodation')),
            'owner' => new OwnerResource($this->whenLoaded('owner')),
            'commission_type' => $this->commission_type,
            'rate' => (float) $this->rate,
            'amount' => (float) $this->amount,
            'currency' => $this->currency,
            'status' => $this->status,
            'invoice_number' => $this->invoice_number,
            'invoice_date' => $this->invoice_date,
            'due_date' => $this->due_date,
            'paid_at' => $this->paid_at,
            'notes' => $this->notes,
            'created_at' => $this->created_at,
        ];
    }
}
