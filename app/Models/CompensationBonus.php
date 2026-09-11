<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompensationBonus extends Model
{
    protected $fillable = [
        'compensation_plan_id',
        'name',
        'description',
        'example',
        'advantage',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
        ];
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(CompensationPlan::class, 'compensation_plan_id');
    }
}
