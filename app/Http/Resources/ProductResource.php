<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'company_id' => $this->company_id,
            'category_id' => $this->category_id,
            'code' => $this->code,
            'name' => $this->name,
            'image' => $this->image,
            'description' => $this->description,
            'technical_sheet' => $this->technical_sheet,
            'price' => $this->price,
            'currency' => $this->currency ?: 'USD',
            'is_active' => $this->is_active,
            'countries' => is_array($this->countries) ? array_values($this->countries) : [],
            'company' => new CompanyResource($this->whenLoaded('company')),
            'category' => new CategoryResource($this->whenLoaded('category')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
