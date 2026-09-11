<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CompensationRankResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'company_id' => $this->company_id,
            'compensation_plan_id' => $this->compensation_plan_id,
            'plan_name' => $this->whenLoaded('plan', fn () => $this->plan?->name),
            'name' => $this->name,
            'description' => $this->description,
            'example' => $this->example,
            'requirements' => $this->requirements,
            'sort_order' => $this->sort_order,
        ];
    }
}
