<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Company;
use App\Models\Product;
use App\Models\WellnessNeed;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class WellnessNeedService
{
    /**
     * @param  array<string, mixed>  $payload
     */
    public function create(int $companyId, array $payload): WellnessNeed
    {
        Company::query()->findOrFail($companyId);

        return DB::transaction(function () use ($companyId, $payload): WellnessNeed {
            $need = WellnessNeed::query()->create([
                'company_id' => $companyId,
                'name' => $payload['name'],
                'description' => $payload['description'] ?? null,
                'image' => $payload['image'] ?? null,
                'sort_order' => $payload['sort_order'] ?? 0,
            ]);

            $this->syncItems($need, $payload['items'] ?? []);

            return $need->load('items.product');
        });
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    public function update(WellnessNeed $need, array $payload): WellnessNeed
    {
        return DB::transaction(function () use ($need, $payload): WellnessNeed {
            $need->update(collect($payload)->only([
                'name',
                'description',
                'image',
                'sort_order',
            ])->all());

            if (array_key_exists('items', $payload)) {
                $this->syncItems($need, $payload['items'] ?? []);
            }

            return $need->fresh('items.product');
        });
    }

    /**
     * @param  list<array<string, mixed>>  $items
     */
    private function syncItems(WellnessNeed $need, array $items): void
    {
        $need->items()->delete();

        foreach ($items as $item) {
            $this->assertProductBelongsToCompany((int) $item['product_id'], $need->company_id);

            $need->items()->create([
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'] ?? 1,
                'notes' => $item['notes'] ?? null,
            ]);
        }
    }

    private function assertProductBelongsToCompany(int $productId, int $companyId): void
    {
        $belongs = Product::query()
            ->where('id', $productId)
            ->where('company_id', $companyId)
            ->exists();

        if (! $belongs) {
            throw new InvalidArgumentException('El producto no pertenece a esta empresa.');
        }
    }
}
