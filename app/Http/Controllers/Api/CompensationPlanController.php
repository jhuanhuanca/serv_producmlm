<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCompensationPlanRequest;
use App\Http\Requests\UpdateCompensationPlanRequest;
use App\Http\Resources\CompensationPlanResource;
use App\Models\CompensationPlan;
use App\Services\CompensationPlanService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CompensationPlanController extends Controller
{
    public function __construct(
        private readonly CompensationPlanService $plans,
    ) {}

    public function index(int $companyId): AnonymousResourceCollection
    {
        return CompensationPlanResource::collection(
            $this->plans->paginateForCompany($companyId)
        );
    }

    public function store(StoreCompensationPlanRequest $request, int $companyId): JsonResponse
    {
        $plan = $this->plans->create($companyId, $request->validated());

        return (new CompensationPlanResource($plan))
            ->response()
            ->setStatusCode(201);
    }

    public function update(UpdateCompensationPlanRequest $request, int $id): CompensationPlanResource
    {
        $plan = CompensationPlan::query()->findOrFail($id);

        return new CompensationPlanResource(
            $this->plans->update($plan, $request->validated())
        );
    }
}
