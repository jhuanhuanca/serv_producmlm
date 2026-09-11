<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CompensationPlan extends Model
{
    protected $fillable = [
        'company_id',
        'name',
        'network_type',
        'network_description',
        'network_example',
        'network_advantages',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function ranks(): HasMany
    {
        return $this->hasMany(CompensationRank::class)->orderBy('sort_order');
    }

    public function packages(): HasMany
    {
        return $this->hasMany(CompensationPackage::class)->orderBy('sort_order');
    }

    public function bonuses(): HasMany
    {
        return $this->hasMany(CompensationBonus::class)->orderBy('sort_order');
    }
}
