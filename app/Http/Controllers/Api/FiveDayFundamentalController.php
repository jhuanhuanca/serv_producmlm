<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreFiveDayFundamentalRequest;
use App\Http\Requests\UpdateFiveDayFundamentalRequest;
use App\Http\Resources\FiveDayFundamentalResource;
use App\Models\Company;
use App\Models\FiveDayFundamental;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class FiveDayFundamentalController extends Controller
{
    public function index(int $companyId): AnonymousResourceCollection
    {
        Company::query()->findOrFail($companyId);

        return FiveDayFundamentalResource::collection(
            FiveDayFundamental::query()
                ->where('company_id', $companyId)
                ->orderBy('sort_order')
                ->orderBy('day_number')
                ->paginate(20)
        );
    }

    public function store(StoreFiveDayFundamentalRequest $request, int $companyId): JsonResponse
    {
        Company::query()->findOrFail($companyId);

        $item = FiveDayFundamental::query()->create([
            ...$request->validated(),
            'company_id' => $companyId,
        ]);

        return (new FiveDayFundamentalResource($item))
            ->response()
            ->setStatusCode(201);
    }

    public function update(UpdateFiveDayFundamentalRequest $request, int $id): FiveDayFundamentalResource
    {
        $item = FiveDayFundamental::query()->findOrFail($id);
        $item->update($request->validated());

        return new FiveDayFundamentalResource($item->fresh());
    }

    public function destroy(int $id): JsonResponse
    {
        FiveDayFundamental::query()->findOrFail($id)->delete();

        return response()->json(['message' => 'Fundamento eliminado']);
    }
}
