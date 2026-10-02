<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventoryImage extends Model
{
    protected $fillable = [
        'uuid',
        'company_id',
        'owner_key',
        'kind',
        'original_name',
        'mime',
        'size',
        'content',
    ];

    protected $hidden = [
        'content',
    ];
}
