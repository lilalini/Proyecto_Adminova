<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OwnerPayoutMethodResource extends JsonResource
{
    public function toArray(Request $request): array
    {
         return [
            'id' => $this->id,
            'owner' => new OwnerResource($this->whenLoaded('owner')),
            'method_type' => $this->method_type,
            'account_holder' => $this->account_holder,
            'account_number' => $this->account_number,
            'bank_name' => $this->bank_name,
            'bank_swift' => $this->bank_swift,
            'is_default' => $this->is_default,
            'is_verified' => $this->is_verified,
            'verified_at' => $this->verified_at,
            'notes' => $this->notes,
            'created_at' => $this->created_at,
        ];
    }
}