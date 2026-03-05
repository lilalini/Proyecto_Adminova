<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LoyaltyPointResource extends JsonResource
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
            'guest' => new GuestResource($this->whenLoaded('guest')),
            'booking' => new BookingResource($this->whenLoaded('booking')),
            'points' => $this->points,
            'type' => $this->type,
            'description' => $this->description,
            'expiry_date' => $this->expiry_date,
            'redeemed_at' => $this->redeemed_at,
            'redeemed_booking' => new BookingResource($this->whenLoaded('redeemedBooking')),
            'adjusted_by' => new UserResource($this->whenLoaded('adjustedBy')),
            'created_at' => $this->created_at,
        ];
    }
}
