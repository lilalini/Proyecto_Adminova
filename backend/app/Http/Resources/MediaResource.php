<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MediaResource extends JsonResource
{
    public function toArray(Request $request): array
{
    return [
        'id' => $this->id,
        'model_type' => $this->model_type,
        'model_id' => $this->model_id,
        'collection_name' => $this->collection_name,
        'name' => $this->name,
        'file_name' => $this->file_name,
        'file_path' => $this->file_path,
        'file_size' => $this->file_size,
        'mime_type' => $this->mime_type,
        'disk' => $this->disk,
        'order' => $this->order,
        'is_main' => $this->is_main,
        'alt_text' => $this->alt_text,
        'title' => $this->title,
        'description' => $this->description,
        'metadata' => $this->metadata,
        'url' => $this->getUrl(), 
        'thumbnail_url' => $this->hasGeneratedConversion('thumb') 
            ? $this->getUrl('thumb') 
            : $this->getUrl(), 
        'created_at' => $this->created_at?->toISOString(),
    ];
}
}