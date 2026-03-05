<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SyncLogResource extends JsonResource
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
            'channel' => new DistributionChannelResource($this->whenLoaded('channel')),
            'sync_type' => $this->sync_type,
            'direction' => $this->direction,
            'status' => $this->status,
            'started_at' => $this->started_at,
            'completed_at' => $this->completed_at,
            'duration_seconds' => $this->duration_seconds,
            'items_total' => $this->items_total,
            'items_success' => $this->items_success,
            'items_failed' => $this->items_failed,
            'request_data' => $this->request_data,
            'response_data' => $this->response_data,
            'error_message' => $this->error_message,
            'error_trace' => $this->error_trace,
            'retry_count' => $this->retry_count,
            'next_retry_at' => $this->next_retry_at,
            'created_by' => new UserResource($this->whenLoaded('createdBy')),
            'created_at' => $this->created_at,
        ];
    }
}
