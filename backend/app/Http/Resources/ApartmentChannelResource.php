<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ApartmentChannelResource extends JsonResource
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
            'distribution_channel' => new DistributionChannelResource($this->whenLoaded('distributionChannel')),
            'external_listing_id' => $this->external_listing_id,
            'external_url' => $this->external_url,
            'connection_status' => $this->connection_status,
            'sync_enabled' => $this->sync_enabled,
            'sync_price' => $this->sync_price,
            'sync_availability' => $this->sync_availability,
            'sync_content' => $this->sync_content,
            'price_adjustment_type' => $this->price_adjustment_type,
            'price_adjustment_value' => $this->price_adjustment_value ? (float) $this->price_adjustment_value : null,
            'min_stay_adjustment' => $this->min_stay_adjustment,
            'last_sync_at' => $this->last_sync_at,
            'last_sync_status' => $this->last_sync_status,
            'created_at' => $this->created_at,
        ];
    }
}
