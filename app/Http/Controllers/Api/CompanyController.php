<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCompanyRequest;
use App\Http\Requests\UpdateCompanyRequest;
use App\Http\Resources\CompanyResource;
use App\Models\Company;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CompanyController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        return CompanyResource::collection(
            Company::query()->withCount('products')->latest()->paginate(20)
        );
    }

    public function show(int $id): CompanyResource
    {
        return new CompanyResource(
            Company::query()->with([
                'products',
                'documents',
                'compensationPlans.ranks',
                'compensationPlans.packages',
                'compensationPlans.bonuses',
                'fiveDayFundamentals',
                'starProducts.product',
                'wellnessNeeds.items.product',
                'imcPackages.items.product',
            ])->findOrFail($id)
        );
    }

    public function store(StoreCompanyRequest $request): JsonResponse
    {
        $company = Company::query()->create($request->validated());

        return (new CompanyResource($company))
            ->response()
            ->setStatusCode(201);
    }

    public function update(UpdateCompanyRequest $request, int $id): CompanyResource
    {
        $company = Company::query()->findOrFail($id);
        $company->update($request->validated());

        return new CompanyResource($company->fresh());
    }

    public function destroy(int $id): JsonResponse
    {
        Company::query()->findOrFail($id)->delete();

        return response()->json(['message' => 'Empresa eliminada']);
    }
}
