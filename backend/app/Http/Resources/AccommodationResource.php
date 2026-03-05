<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AccommodationResource extends JsonResource
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
            'title' => $this->title,
            'slug' => $this->slug,
            'description' => $this->description,
            'property_type' => $this->property_type,
            'bedrooms' => $this->bedrooms,
            'bathrooms' => $this->bathrooms,
            'max_guests' => $this->max_guests,
            'size_m2' => $this->size_m2,
            'address' => $this->address,
            'city' => $this->city,
            'postal_code' => $this->postal_code,
            'country' => $this->country,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'base_price' => (float) $this->base_price,
            'cleaning_fee' => (float) $this->cleaning_fee,
            'security_deposit' => (float) $this->security_deposit,
            'minimum_stay' => $this->minimum_stay,
            'maximum_stay' => $this->maximum_stay,
            'check_in_time' => $this->check_in_time,
            'check_out_time' => $this->check_out_time,
            'status' => $this->status,
            'amenities' => $this->amenities,
            'house_rules' => $this->house_rules,
            
            // Relaciones
            'owner' => [
                'id' => $this->owner->id,
                'first_name' => $this->owner->first_name,
                'last_name' => $this->owner->last_name,
                'email' => $this->owner->email,
            ],
            'cancellation_policy' => [
                'id' => $this->cancellationPolicy->id,
                'name' => $this->cancellationPolicy->name,
                'free_cancellation_days' => $this->cancellationPolicy->free_cancellation_days,
                'penalty_percentage' => (float) $this->cancellationPolicy->penalty_percentage,
            ],
            
            // Media (fotos)
           /* 'main_image' => $this->media->where('is_main', true)->first()?->file_path,
            'images' => $this->media->map(fn($media) => [
                'id' => $media->id,
                'url' => $media->file_path,
                'is_main' => $media->is_main,
            ]),
            */
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
