<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FiveDayFundamental extends Model
{
    protected $fillable = [
        'company_id',
        'day_number',
        'title',
        'description',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'day_number' => 'integer',
            'sort_order' => 'integer',
        ];
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}
