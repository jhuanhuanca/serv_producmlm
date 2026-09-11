<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ImcPackage extends Model
{
    public const GOAL_LOSE_WEIGHT = 'lose_weight';

    public const GOAL_GAIN_WEIGHT = 'gain_weight';

    public const GOALS = [
        self::GOAL_LOSE_WEIGHT,
        self::GOAL_GAIN_WEIGHT,
    ];

    protected $fillable = [
        'company_id',
        'goal',
        'name',
        'description',
        'image',
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

    public function items(): HasMany
    {
        return $this->hasMany(ImcPackageItem::class);
    }
}
