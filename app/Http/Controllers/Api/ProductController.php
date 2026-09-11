<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Services\ProductService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ProductController extends Controller
{
    public function __construct(
        private readonly ProductService $products,
    ) {}

    public function index(Request $request): AnonymousResourceCollection
    {
        return ProductResource::collection($this->products->paginate($request));
    }

    public function show(int $id): ProductResource
    {
        return new ProductResource($this->products->find($id));
    }

    public function store(StoreProductRequest $request): JsonResponse
    {
        $product = Product::query()->create($this->payload($request->validated()));

        return (new ProductResource($product->load(['company', 'category'])))
            ->response()
            ->setStatusCode(201);
    }

    public function update(UpdateProductRequest $request, int $id): ProductResource
    {
        $product = Product::query()->findOrFail($id);
        $product->update($this->payload($request->validated()));

        return new ProductResource($product->fresh(['company', 'category']));
    }

    public function destroy(int $id): JsonResponse
    {
        Product::query()->findOrFail($id)->delete();

        return response()->json(['message' => 'Producto eliminado']);
    }

    /** @param  array<string, mixed>  $data */
    private function payload(array $data): array
    {
        if (array_key_exists('countries', $data) && $data['countries'] === []) {
            $data['countries'] = null;
        }

        if (isset($data['currency'])) {
            $data['currency'] = strtoupper(trim((string) $data['currency']));
        }

        return $data;
    }
}
