<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Support\MediaPlayback;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DocumentResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        $playback = MediaPlayback::describe((string) $this->file_type, (string) $this->file_path);

        return [
            'id' => $this->id,
            'company_id' => $this->company_id,
            'title' => $this->title,
            'description' => $this->description,
            'file_path' => $this->file_path,
            'url' => $this->file_path,
            'file_type' => $this->file_type,
            'kind' => $playback['kind'],
            'player' => $playback['player'],
            'embed_url' => $playback['embed_url'],
            'original_name' => $this->original_name,
            'thumbnail' => $this->thumbnail,
            'sort_order' => $this->sort_order,
            'is_active' => $this->is_active,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
