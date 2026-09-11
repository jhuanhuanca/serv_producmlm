<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTechnicalSheetRequest;
use App\Http\Requests\UpdateTechnicalSheetRequest;
use App\Http\Resources\TechnicalSheetResource;
use App\Models\Product;
use Illuminate\Http\JsonResponse;

class TechnicalSheetController extends Controller
{
    public function show(int $productId): TechnicalSheetResource
    {
        $product = Product::query()->findOrFail($productId);

        abort_if(blank($product->technical_sheet), 404, 'El producto no tiene ficha técnica.');

        return new TechnicalSheetResource($product);
    }

    public function store(StoreTechnicalSheetRequest $request, int $productId): JsonResponse
    {
        $product = Product::query()->findOrFail($productId);
        $product->update($request->validated());

        return (new TechnicalSheetResource($product->fresh()))
            ->response()
            ->setStatusCode(201);
    }

    public function update(UpdateTechnicalSheetRequest $request, int $id): TechnicalSheetResource
    {
        $product = Product::query()->findOrFail($id);
        $product->update($request->validated());

        return new TechnicalSheetResource($product->fresh());
    }
}
