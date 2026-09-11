<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    protected $fillable = [
        'company_id',
        'category_id',
        'code',
        'name',
        'image',
        'description',
        'technical_sheet',
        'price',
        'currency',
        'is_active',
        'countries',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'is_active' => 'boolean',
            'countries' => 'array',
        ];
    }

    public function scopeAvailableInCountry(Builder $query, ?string $country): Builder
    {
        $code = strtoupper(trim((string) $country));

        if ($code === '') {
            return $query;
        }

        return $query->where(function (Builder $builder) use ($code): void {
            $builder->where(function (Builder $empty): void {
                $empty->whereNull('countries')
                    ->orWhereJsonLength('countries', 0);
            })->orWhereJsonContains('countries', $code);
        });
    }

    public function isAvailableIn(?string $country): bool
    {
        $codes = $this->countries;

        if (! is_array($codes) || $codes === []) {
            return true;
        }

        $code = strtoupper(trim((string) $country));

        if ($code === '') {
            return true;
        }

        return in_array($code, array_map(
            static fn (mixed $item): string => strtoupper(trim((string) $item)),
            $codes,
        ), true);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function starProductEntries(): HasMany
    {
        return $this->hasMany(StarProduct::class);
    }

    public function wellnessNeedItems(): HasMany
    {
        return $this->hasMany(WellnessNeedItem::class);
    }

    public function imcPackageItems(): HasMany
    {
        return $this->hasMany(ImcPackageItem::class);
    }
}
