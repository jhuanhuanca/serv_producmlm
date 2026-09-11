<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SupportTicketReplyResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'author_role' => $this->author_role,
            'author_name' => $this->author_name,
            'message' => $this->message,
            'created_at' => $this->created_at,
        ];
    }
}
