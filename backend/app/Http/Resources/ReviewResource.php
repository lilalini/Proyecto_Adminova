<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReviewResource extends JsonResource
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
            'accommodation' => new AccommodationResource($this->whenLoaded('accommodation')),
            'booking' => new BookingResource($this->whenLoaded('booking')),
            'guest' => new GuestResource($this->whenLoaded('guest')),
            'user' => new UserResource($this->whenLoaded('user')),
            'rating' => $this->rating,
            'cleanliness_rating' => $this->cleanliness_rating,
            'communication_rating' => $this->communication_rating,
            'location_rating' => $this->location_rating,
            'value_rating' => $this->value_rating,
            'title' => $this->title,
            'comment' => $this->comment,
            'host_response' => $this->host_response,
            'host_responded_at' => $this->host_responded_at,
            'status' => $this->status,
            'source' => $this->source,
            'is_verified' => $this->is_verified,
            'helpful_votes' => $this->helpful_votes,
            'published_at' => $this->published_at,
            'created_at' => $this->created_at,
        ];
    }
}
