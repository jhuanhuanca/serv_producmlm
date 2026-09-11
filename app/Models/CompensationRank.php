<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompensationRank extends Model
{
    protected $fillable = [
        'company_id',
        'compensation_plan_id',
        'name',
        'description',
        'example',
        'requirements',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
        ];
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(CompensationPlan::class, 'compensation_plan_id');
    }
}
