<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCompanyRankRequest;
use App\Http\Requests\UpdateCompanyRankRequest;
use App\Http\Resources\CompensationRankResource;
use App\Models\Company;
use App\Models\CompensationPlan;
use App\Models\CompensationRank;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Validation\ValidationException;

class CompanyRankController extends Controller
{
    public function index(int $companyId): AnonymousResourceCollection
    {
        Company::query()->findOrFail($companyId);

        return CompensationRankResource::collection(
            CompensationRank::query()
                ->where('company_id', $companyId)
                ->with('plan:id,name')
                ->orderBy('sort_order')
                ->orderBy('name')
                ->paginate(50)
        );
    }

    public function store(StoreCompanyRankRequest $request, int $companyId): JsonResponse
    {
        Company::query()->findOrFail($companyId);
        $data = $request->validated();
        $this->assertPlanBelongsToCompany($companyId, $data['compensation_plan_id'] ?? null);

        $rank = CompensationRank::query()->create([
            ...$data,
            'company_id' => $companyId,
        ]);

        return (new CompensationRankResource($rank->load('plan:id,name')))
            ->response()
            ->setStatusCode(201);
    }

    public function update(UpdateCompanyRankRequest $request, int $id): CompensationRankResource
    {
        $rank = CompensationRank::query()->findOrFail($id);
        $data = $request->validated();
        $this->assertPlanBelongsToCompany(
            (int) $rank->company_id,
            $data['compensation_plan_id'] ?? $rank->compensation_plan_id,
        );

        $rank->update($data);

        return new CompensationRankResource($rank->fresh('plan:id,name'));
    }

    public function destroy(int $id): JsonResponse
    {
        CompensationRank::query()->findOrFail($id)->delete();

        return response()->json(['message' => 'Rango eliminado']);
    }

    private function assertPlanBelongsToCompany(int $companyId, mixed $planId): void
    {
        if ($planId === null || $planId === '') {
            return;
        }

        $exists = CompensationPlan::query()
            ->where('id', $planId)
            ->where('company_id', $companyId)
            ->exists();

        if (! $exists) {
            throw ValidationException::withMessages([
                'compensation_plan_id' => ['El plan no pertenece a esta empresa.'],
            ]);
        }
    }
}
