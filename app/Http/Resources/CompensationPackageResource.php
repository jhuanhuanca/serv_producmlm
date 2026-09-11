<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CompensationPackageResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'compensation_plan_id' => $this->compensation_plan_id,
            'name' => $this->name,
            'description' => $this->description,
            'pv' => $this->pv,
            'cost' => $this->cost,
            'image' => $this->image,
            'sort_order' => $this->sort_order,
        ];
    }
}
