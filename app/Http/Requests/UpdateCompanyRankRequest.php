<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCompanyRankRequest extends FormRequest
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
            'description' => ['nullable', 'string'],
            'example' => ['nullable', 'string'],
            'requirements' => ['nullable', 'string'],
            'sort_order' => ['sometimes', 'integer', 'min:0'],
            'compensation_plan_id' => ['nullable', 'integer', 'exists:compensation_plans,id'],
        ];
    }
}
