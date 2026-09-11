<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreImcPackageRequest;
use App\Http\Requests\UpdateImcPackageRequest;
use App\Http\Resources\ImcPackageResource;
use App\Models\Company;
use App\Models\ImcPackage;
use App\Services\ImcPackageService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ImcPackageController extends Controller
{
    public function __construct(
        private readonly ImcPackageService $packages,
    ) {}

    public function index(Request $request, int $companyId): AnonymousResourceCollection
    {
        Company::query()->findOrFail($companyId);

        $query = ImcPackage::query()
            ->where('company_id', $companyId)
            ->with('items.product')
            ->orderBy('sort_order');

        if ($request->filled('goal')) {
            $query->where('goal', $request->string('goal'));
        }

        $perPage = min(100, max(1, $request->integer('per_page', 20)));

        return ImcPackageResource::collection($query->paginate($perPage));
    }

    public function store(StoreImcPackageRequest $request, int $companyId): JsonResponse
    {
        $package = $this->packages->create($companyId, $request->validated());

        return (new ImcPackageResource($package))
            ->response()
            ->setStatusCode(201);
    }

    public function update(UpdateImcPackageRequest $request, int $id): ImcPackageResource
    {
        $package = ImcPackage::query()->findOrFail($id);

        return new ImcPackageResource(
            $this->packages->update($package, $request->validated())
        );
    }

    public function destroy(int $id): JsonResponse
    {
        ImcPackage::query()->findOrFail($id)->delete();

        return response()->json(['message' => 'Paquete IMC eliminado']);
    }
}
