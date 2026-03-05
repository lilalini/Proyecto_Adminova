<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OwnerResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'phone' => $this->phone,
            'document_type' => $this->document_type,
            'document_number' => $this->document_number,
            'address' => $this->address,
            'city' => $this->city,
            'postal_code' => $this->postal_code,
            'country' => $this->country,
            'iban' => $this->iban,
            'commission_rate' => (float) $this->commission_rate,
            'contract_signed' => $this->contract_signed,
            'contract_date' => $this->contract_date,
            'is_active' => $this->is_active,
            'last_login_at' => $this->last_login_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            
            // Relaciones
            'accommodations' => AccommodationResource::collection($this->whenLoaded('accommodations')),
            'payout_methods' => OwnerPayoutMethodResource::collection($this->whenLoaded('payoutMethods')),
        ];
    }
}