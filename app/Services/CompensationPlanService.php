<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Company;
use App\Models\CompensationPlan;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class CompensationPlanService
{
    public function paginateForCompany(int $companyId): LengthAwarePaginator
    {
        Company::query()->findOrFail($companyId);

        return CompensationPlan::query()
            ->where('company_id', $companyId)
            ->with(['ranks', 'packages', 'bonuses'])
            ->latest()
            ->paginate(20);
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    public function create(int $companyId, array $payload): CompensationPlan
    {
        Company::query()->findOrFail($companyId);

        return DB::transaction(function () use ($companyId, $payload): CompensationPlan {
            $plan = CompensationPlan::query()->create([
                'company_id' => $companyId,
                'name' => $payload['name'],
                'network_type' => $payload['network_type'],
                'network_description' => $payload['network_description'] ?? null,
                'network_example' => $payload['network_example'] ?? null,
                'network_advantages' => $payload['network_advantages'] ?? null,
                'is_active' => $payload['is_active'] ?? true,
            ]);

            $this->syncChildren($plan, $payload);

            return $plan->load(['ranks', 'packages', 'bonuses']);
        });
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    public function update(CompensationPlan $plan, array $payload): CompensationPlan
    {
        return DB::transaction(function () use ($plan, $payload): CompensationPlan {
            $plan->update(collect($payload)->only([
                'name',
                'network_type',
                'network_description',
                'network_example',
                'network_advantages',
                'is_active',
            ])->all());

            $this->syncChildren($plan, $payload);

            return $plan->fresh(['ranks', 'packages', 'bonuses']);
        });
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    private function syncChildren(CompensationPlan $plan, array $payload): void
    {
        if (array_key_exists('ranks', $payload)) {
            $plan->ranks()->delete();
            foreach ($payload['ranks'] ?? [] as $index => $rank) {
                $plan->ranks()->create([
                    'company_id' => $plan->company_id,
                    'name' => $rank['name'],
                    'description' => $rank['description'] ?? null,
                    'example' => $rank['example'] ?? null,
                    'requirements' => $rank['requirements'] ?? null,
                    'sort_order' => $rank['sort_order'] ?? $index,
                ]);
            }
        }

        if (array_key_exists('packages', $payload)) {
            $plan->packages()->delete();
            foreach ($payload['packages'] ?? [] as $index => $package) {
                $plan->packages()->create([
                    'name' => $package['name'],
                    'description' => $package['description'] ?? null,
                    'pv' => $package['pv'] ?? 0,
                    'cost' => $package['cost'] ?? 0,
                    'image' => $package['image'] ?? null,
                    'sort_order' => $package['sort_order'] ?? $index,
                ]);
            }
        }

        if (array_key_exists('bonuses', $payload)) {
            $plan->bonuses()->delete();
            foreach ($payload['bonuses'] ?? [] as $index => $bonus) {
                $plan->bonuses()->create([
                    'name' => $bonus['name'],
                    'description' => $bonus['description'] ?? null,
                    'example' => $bonus['example'] ?? null,
                    'advantage' => $bonus['advantage'] ?? null,
                    'sort_order' => $bonus['sort_order'] ?? $index,
                ]);
            }
        }
    }
}
