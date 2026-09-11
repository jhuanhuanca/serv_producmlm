<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** Compatibilidad: la ficha técnica ahora vive en products.technical_sheet. */
class TechnicalSheetResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        return [
            'product_id' => $this->id,
            'technical_sheet' => $this->technical_sheet,
            'updated_at' => $this->updated_at,
        ];
    }
}
