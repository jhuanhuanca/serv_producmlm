<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCompensationPlanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'string', 'max:255'],
            'network_type' => ['sometimes', 'string', 'max:80'],
            'network_description' => ['nullable', 'string'],
            'network_example' => ['nullable', 'string'],
            'network_advantages' => ['nullable', 'string'],
            'is_active' => ['sometimes', 'boolean'],
            'ranks' => ['sometimes', 'array'],
            'ranks.*.name' => ['required_with:ranks', 'string', 'max:255'],
            'ranks.*.description' => ['nullable', 'string'],
            'ranks.*.example' => ['nullable', 'string'],
            'ranks.*.requirements' => ['nullable', 'string'],
            'ranks.*.sort_order' => ['sometimes', 'integer', 'min:0'],
            'packages' => ['sometimes', 'array'],
            'packages.*.name' => ['required_with:packages', 'string', 'max:255'],
            'packages.*.description' => ['nullable', 'string'],
            'packages.*.pv' => ['sometimes', 'numeric', 'min:0'],
            'packages.*.cost' => ['sometimes', 'numeric', 'min:0'],
            'packages.*.image' => ['nullable', 'string', 'max:500'],
            'packages.*.sort_order' => ['sometimes', 'integer', 'min:0'],
            'bonuses' => ['sometimes', 'array'],
            'bonuses.*.name' => ['required_with:bonuses', 'string', 'max:255'],
            'bonuses.*.description' => ['nullable', 'string'],
            'bonuses.*.example' => ['nullable', 'string'],
            'bonuses.*.advantage' => ['nullable', 'string'],
            'bonuses.*.sort_order' => ['sometimes', 'integer', 'min:0'],
        ];
    }
}
