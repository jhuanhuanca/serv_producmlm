<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Support\CatalogTools;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CompanyResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'logo' => $this->logo,
            'color_palette' => $this->color_palette,
            'website' => $this->website,
            'is_active' => $this->is_active,
            'enabled_tools' => $this->resource->resolvedEnabledTools(),
            'available_tools' => CatalogTools::KEYS,
            'products_count' => $this->whenCounted('products'),
            'products' => ProductResource::collection($this->whenLoaded('products')),
            'documents' => DocumentResource::collection($this->whenLoaded('documents')),
            'compensation_plans' => CompensationPlanResource::collection($this->whenLoaded('compensationPlans')),
            'ranks' => CompensationRankResource::collection($this->whenLoaded('ranks')),
            'five_day_fundamentals' => FiveDayFundamentalResource::collection($this->whenLoaded('fiveDayFundamentals')),
            'star_products' => StarProductResource::collection($this->whenLoaded('starProducts')),
            'wellness_needs' => WellnessNeedResource::collection($this->whenLoaded('wellnessNeeds')),
            'imc_packages' => ImcPackageResource::collection($this->whenLoaded('imcPackages')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
