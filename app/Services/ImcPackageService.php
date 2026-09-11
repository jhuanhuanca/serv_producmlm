<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Company;
use App\Models\ImcPackage;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class ImcPackageService
{
    /**
     * @param  array<string, mixed>  $payload
     */
    public function create(int $companyId, array $payload): ImcPackage
    {
        Company::query()->findOrFail($companyId);

        return DB::transaction(function () use ($companyId, $payload): ImcPackage {
            $package = ImcPackage::query()->create([
                'company_id' => $companyId,
                'goal' => $payload['goal'],
                'name' => $payload['name'],
                'description' => $payload['description'] ?? null,
                'image' => $payload['image'] ?? null,
                'sort_order' => $payload['sort_order'] ?? 0,
            ]);

            $this->syncItems($package, $payload['items'] ?? []);

            return $package->load('items.product');
        });
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    public function update(ImcPackage $package, array $payload): ImcPackage
    {
        return DB::transaction(function () use ($package, $payload): ImcPackage {
            $package->update(collect($payload)->only([
                'goal',
                'name',
                'description',
                'image',
                'sort_order',
            ])->all());

            if (array_key_exists('items', $payload)) {
                $this->syncItems($package, $payload['items'] ?? []);
            }

            return $package->fresh('items.product');
        });
    }

    /**
     * @param  list<array<string, mixed>>  $items
     */
    private function syncItems(ImcPackage $package, array $items): void
    {
        $package->items()->delete();

        foreach ($items as $item) {
            $this->assertProductBelongsToCompany((int) $item['product_id'], $package->company_id);

            $package->items()->create([
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
