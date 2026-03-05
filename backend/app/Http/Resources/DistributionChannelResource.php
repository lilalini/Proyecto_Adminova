<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DistributionChannelResource extends JsonResource
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
            'channel_code' => $this->channel_code,
            'name' => $this->name,
            'channel_type' => $this->channel_type,
            'commission_rate' => (float) $this->commission_rate,
            'is_active' => $this->is_active,
        ];
    }
}
