<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompensationPackage extends Model
{
    protected $fillable = [
        'compensation_plan_id',
        'name',
        'description',
        'pv',
        'cost',
        'image',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'pv' => 'decimal:2',
            'cost' => 'decimal:2',
            'sort_order' => 'integer',
        ];
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(CompensationPlan::class, 'compensation_plan_id');
    }
}
