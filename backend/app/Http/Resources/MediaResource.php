<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MediaResource extends JsonResource
{
    /**
     * Transforma el recurso en un array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var \App\Models\Media $media */
        $media = $this->resource;

        return [
            'id' => $media->id,
            'model_type' => $media->model_type,
            'model_id' => $media->model_id,
            'collection_name' => $media->collection_name,
            'name' => $media->name,
            'file_name' => $media->file_name,
            'file_path' => $media->file_path,
            'file_size' => $media->file_size,
            'mime_type' => $media->mime_type,
            'disk' => $media->disk,
            'order' => $media->order,
            'is_main' => $media->is_main,
            'alt_text' => $media->alt_text,
            'title' => $media->title,
            'description' => $media->description,
            'metadata' => $media->metadata,
            'url' => $media->url,
            'thumbnail_url' => $media->thumbnail_url,
            'human_readable_size' => $media->getHumanReadableSize(),
            'is_image' => $media->isImage(),
            'created_at' => $media->created_at?->toISOString(),
        ];
    }
}