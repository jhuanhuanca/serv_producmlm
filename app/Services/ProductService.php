<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;

class ProductService
{
    public function paginate(Request $request): LengthAwarePaginator
    {
        $query = Product::query()->with(['company', 'category']);

        if ($request->filled('company_id')) {
            $query->where('company_id', $request->integer('company_id'));
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->integer('category_id'));
        }

        if ($request->filled('search')) {
            $search = (string) $request->string('search');
            $query->where(function ($builder) use ($search): void {
                $builder->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%");
            });
        }

        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->input('min_price'));
        }

        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->input('max_price'));
        }

        if ($request->has('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        if ($request->filled('country')) {
            $query->availableInCountry((string) $request->string('country'));
        }

        $perPage = min(100, max(1, $request->integer('per_page', 20)));

        return $query->latest()->paginate($perPage);
    }

    public function find(int $id): Product
    {
        return Product::query()
            ->with(['company', 'category'])
            ->findOrFail($id);
    }
}
