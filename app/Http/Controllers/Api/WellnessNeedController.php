<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreWellnessNeedRequest;
use App\Http\Requests\UpdateWellnessNeedRequest;
use App\Http\Resources\WellnessNeedResource;
use App\Models\Company;
use App\Models\WellnessNeed;
use App\Services\WellnessNeedService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class WellnessNeedController extends Controller
{
    public function __construct(
        private readonly WellnessNeedService $needs,
    ) {}

    public function index(Request $request, int $companyId): AnonymousResourceCollection
    {
        Company::query()->findOrFail($companyId);

        $perPage = min(100, max(1, $request->integer('per_page', 20)));

        return WellnessNeedResource::collection(
            WellnessNeed::query()
                ->where('company_id', $companyId)
                ->with('items.product')
                ->orderBy('sort_order')
                ->paginate($perPage)
        );
    }

    public function store(StoreWellnessNeedRequest $request, int $companyId): JsonResponse
    {
        $need = $this->needs->create($companyId, $request->validated());

        return (new WellnessNeedResource($need))
            ->response()
            ->setStatusCode(201);
    }

    public function update(UpdateWellnessNeedRequest $request, int $id): WellnessNeedResource
    {
        $need = WellnessNeed::query()->findOrFail($id);

        return new WellnessNeedResource(
            $this->needs->update($need, $request->validated())
        );
    }

    public function destroy(int $id): JsonResponse
    {
        WellnessNeed::query()->findOrFail($id)->delete();

        return response()->json(['message' => 'Paquete de bienestar eliminado']);
    }
}
