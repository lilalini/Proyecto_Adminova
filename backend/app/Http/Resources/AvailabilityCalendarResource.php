<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AvailabilityCalendarResource extends JsonResource
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
            'accommodation_id' => $this->accommodation_id,
            'date' => $this->date,
            'status' => $this->status,
            'price' => $this->price ? (float) $this->price : null,
            'min_nights' => $this->min_nights,
            'max_nights' => $this->max_nights,
            'closed_to_arrival' => $this->closed_to_arrival,
            'closed_to_departure' => $this->closed_to_departure,
            'notes' => $this->notes,
        ];
    }
}
