<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ImcPackageItemResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'imc_package_id' => $this->imc_package_id,
            'product_id' => $this->product_id,
            'quantity' => $this->quantity,
            'notes' => $this->notes,
            'product' => new ProductResource($this->whenLoaded('product')),
        ];
    }
}
