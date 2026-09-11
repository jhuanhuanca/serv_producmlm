<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WellnessNeedItemResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'wellness_need_id' => $this->wellness_need_id,
            'product_id' => $this->product_id,
            'quantity' => $this->quantity,
            'notes' => $this->notes,
            'product' => new ProductResource($this->whenLoaded('product')),
        ];
    }
}
