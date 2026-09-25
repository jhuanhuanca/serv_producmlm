<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Support\CatalogTools;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCompanyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'logo' => ['nullable', 'string', 'max:500'],
            'color_palette' => ['nullable', 'array'],
            'color_palette.primary' => ['nullable', 'string', 'max:20'],
            'color_palette.secondary' => ['nullable', 'string', 'max:20'],
            'color_palette.accent' => ['nullable', 'string', 'max:20'],
            'website' => ['nullable', 'url', 'max:255'],
            'is_active' => ['sometimes', 'boolean'],
            'enabled_tools' => ['nullable', 'array'],
            'enabled_tools.*' => ['string', Rule::in(CatalogTools::KEYS)],
        ];
    }
}
