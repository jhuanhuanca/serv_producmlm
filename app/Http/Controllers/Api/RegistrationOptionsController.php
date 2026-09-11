<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Company;
use Illuminate\Http\JsonResponse;

class RegistrationOptionsController extends Controller
{
    public function __invoke(): JsonResponse
    {
        $companies = Company::query()
            ->where('is_active', true)
            ->with(['ranks' => fn ($query) => $query->orderBy('sort_order')->orderBy('name')])
            ->orderBy('name')
            ->get();

        return response()->json([
            'data' => $companies->map(function (Company $company): array {
                return [
                    'id' => $company->id,
                    'name' => $company->name,
                    'slug' => $company->slug,
                    'ranks' => $company->ranks->map(fn ($rank): array => [
                        'id' => $rank->id,
                        'name' => $rank->name,
                    ])->values(),
                ];
            })->values(),
        ]);
    }
}
