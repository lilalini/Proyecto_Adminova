<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\AccommodationResource;
use App\Http\Resources\GuestResource;
use App\Http\Resources\DistributionChannelResource;

class BookingResource extends JsonResource
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
            'booking_reference' => $this->booking_reference,
            'accommodation' => new AccommodationResource($this->whenLoaded('accommodation')),
            'guest' => new GuestResource($this->whenLoaded('guest')),
            'channel' => new DistributionChannelResource($this->whenLoaded('channel')),
            'check_in' => $this->check_in,
            'check_out' => $this->check_out,
            'nights' => $this->nights,
            'adults' => $this->adults,
            'children' => $this->children,
            'infants' => $this->infants,
            'pets' => $this->pets,
            'status' => $this->status,
            'total_amount' => (float) $this->total_amount,
            'paid_amount' => (float) $this->paid_amount,
            'balance_due' => (float) $this->balance_due,
            'payment_status' => $this->payment_status,
            'guest_name' => $this->guest_name,
            'guest_email' => $this->guest_email,
            'guest_phone' => $this->guest_phone,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
