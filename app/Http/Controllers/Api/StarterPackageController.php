<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreStarterPackageRequest;
use App\Http\Requests\UpdateStarterPackageRequest;
use App\Http\Resources\StarterPackageResource;
use App\Models\Company;
use App\Models\StarterPackage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class StarterPackageController extends Controller
{
    public function index(int $companyId): AnonymousResourceCollection
    {
        Company::query()->findOrFail($companyId);

        return StarterPackageResource::collection(
            StarterPackage::query()
                ->where('company_id', $companyId)
                ->orderBy('sort_order')
                ->orderBy('title')
                ->paginate(20)
        );
    }

    public function store(StoreStarterPackageRequest $request, int $companyId): JsonResponse
    {
        Company::query()->findOrFail($companyId);

        $item = StarterPackage::query()->create([
            ...$request->validated(),
            'company_id' => $companyId,
        ]);

        return (new StarterPackageResource($item))
            ->response()
            ->setStatusCode(201);
    }

    public function update(UpdateStarterPackageRequest $request, int $id): StarterPackageResource
    {
        $item = StarterPackage::query()->findOrFail($id);
        $item->update($request->validated());

        return new StarterPackageResource($item->fresh());
    }

    public function destroy(int $id): JsonResponse
    {
        StarterPackage::query()->findOrFail($id)->delete();

        return response()->json(['message' => 'Paquete de inicio eliminado']);
    }
}
