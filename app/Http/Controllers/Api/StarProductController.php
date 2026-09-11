<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreStarProductRequest;
use App\Http\Requests\UpdateStarProductRequest;
use App\Http\Resources\StarProductResource;
use App\Models\Company;
use App\Models\Product;
use App\Models\StarProduct;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use InvalidArgumentException;

class StarProductController extends Controller
{
    public function index(int $companyId): AnonymousResourceCollection
    {
        Company::query()->findOrFail($companyId);

        return StarProductResource::collection(
            StarProduct::query()
                ->where('company_id', $companyId)
                ->with('product')
                ->orderBy('sort_order')
                ->paginate(20)
        );
    }

    public function store(StoreStarProductRequest $request, int $companyId): JsonResponse
    {
        Company::query()->findOrFail($companyId);
        $this->assertProductBelongsToCompany($request->integer('product_id'), $companyId);

        $item = StarProduct::query()->create([
            ...$request->validated(),
            'company_id' => $companyId,
        ]);

        return (new StarProductResource($item->load('product')))
            ->response()
            ->setStatusCode(201);
    }

    public function update(UpdateStarProductRequest $request, int $id): StarProductResource
    {
        $item = StarProduct::query()->findOrFail($id);

        if ($request->filled('product_id')) {
            $this->assertProductBelongsToCompany($request->integer('product_id'), $item->company_id);
        }

        $item->update($request->validated());

        return new StarProductResource($item->fresh('product'));
    }

    public function destroy(int $id): JsonResponse
    {
        StarProduct::query()->findOrFail($id)->delete();

        return response()->json(['message' => 'Producto estrella eliminado']);
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
