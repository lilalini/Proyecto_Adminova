<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MessageResource extends JsonResource
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
            'sender' => $this->whenLoaded('sender'),
            'receiver' => $this->whenLoaded('receiver'),
            'accommodation' => new AccommodationResource($this->whenLoaded('accommodation')),
            'booking' => new BookingResource($this->whenLoaded('booking')),
            'parent' => new MessageResource($this->whenLoaded('parent')),
            'subject' => $this->subject,
            'body' => $this->body,
            'is_read' => $this->is_read,
            'read_at' => $this->read_at,
            'message_type' => $this->message_type,
            'attachments' => $this->attachments,
            'priority' => $this->priority,
            'sent_at' => $this->sent_at,
            'created_at' => $this->created_at,
        ];
    }
}
