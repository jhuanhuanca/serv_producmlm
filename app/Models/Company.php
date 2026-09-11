<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Company extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'logo',
        'color_palette',
        'website',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'color_palette' => 'array',
            'is_active' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Company $company): void {
            if (filled($company->slug)) {
                return;
            }

            $company->slug = Str::slug($company->name).'-'.Str::lower(Str::random(6));
        });
    }

    public function categories(): HasMany
    {
        return $this->hasMany(Category::class);
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(Document::class);
    }

    public function compensationPlans(): HasMany
    {
        return $this->hasMany(CompensationPlan::class);
    }

    public function ranks(): HasMany
    {
        return $this->hasMany(CompensationRank::class)->orderBy('sort_order')->orderBy('name');
    }

    public function fiveDayFundamentals(): HasMany
    {
        return $this->hasMany(FiveDayFundamental::class)->orderBy('sort_order');
    }

    public function starProducts(): HasMany
    {
        return $this->hasMany(StarProduct::class)->orderBy('sort_order');
    }

    public function wellnessNeeds(): HasMany
    {
        return $this->hasMany(WellnessNeed::class)->orderBy('sort_order');
    }

    public function imcPackages(): HasMany
    {
        return $this->hasMany(ImcPackage::class)->orderBy('sort_order');
    }

    public function starterPackages(): HasMany
    {
        return $this->hasMany(StarterPackage::class)->orderBy('sort_order')->orderBy('title');
    }
}
