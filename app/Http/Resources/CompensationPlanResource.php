<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CompensationPlanResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'company_id' => $this->company_id,
            'name' => $this->name,
            'network_type' => $this->network_type,
            'network_description' => $this->network_description,
            'network_example' => $this->network_example,
            'network_advantages' => $this->network_advantages,
            'is_active' => $this->is_active,
            'ranks' => CompensationRankResource::collection($this->whenLoaded('ranks')),
            'packages' => CompensationPackageResource::collection($this->whenLoaded('packages')),
            'bonuses' => CompensationBonusResource::collection($this->whenLoaded('bonuses')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
