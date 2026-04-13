<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\UserResource;

class DocumentResource extends JsonResource
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
            'documentable_type' => $this->documentable_type,
            'documentable_id' => $this->documentable_id,
            'document_type' => $this->document_type,
            'title' => $this->title,
            'file_name' => $this->file_name,
            'file_path' => $this->file_path,
            'file_size' => $this->file_size,
            'file_url' => $this->file_path 
                ? asset('storage/' . $this->file_path) 
                : null,
            'mime_type' => $this->mime_type,
            'is_signed' => $this->is_signed,
            'signed_at' => $this->signed_at,
            'valid_from' => $this->valid_from,
            'valid_until' => $this->valid_until,
            'is_verified' => $this->is_verified,
            'verified_by' => new UserResource($this->whenLoaded('verifiedBy')),
            'verified_at' => $this->verified_at,
            'notes' => $this->notes,
            'metadata' => $this->metadata,
            'created_at' => $this->created_at,
        ];
    }
}
