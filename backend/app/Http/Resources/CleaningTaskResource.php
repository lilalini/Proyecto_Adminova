<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CleaningTaskResource extends JsonResource
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
            'assigned_to' => new UserResource($this->whenLoaded('assignedTo')),
            'created_by' => new UserResource($this->whenLoaded('createdBy')),
            'verified_by' => new UserResource($this->whenLoaded('verifiedBy')),
            'task_type' => $this->task_type,
            'priority' => $this->priority,
            'title' => $this->title,
            'description' => $this->description,
            'checklist' => $this->checklist,
            'scheduled_date' => $this->scheduled_date,
            'completed_at' => $this->completed_at,
            'duration_minutes' => $this->duration_minutes,
            'photos' => $this->photos,
            'notes' => $this->notes,
            'status' => $this->status,
            'verified_at' => $this->verified_at,
            'created_at' => $this->created_at,
        ];
    }
}
