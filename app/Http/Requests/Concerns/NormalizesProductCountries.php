<?php

declare(strict_types=1);

namespace App\Http\Requests\Concerns;

use App\Support\CatalogCountries;
use Illuminate\Validation\Rule;

trait NormalizesProductCountries
{
    protected function prepareCountriesForValidation(): void
    {
        if (! $this->exists('countries')) {
            return;
        }

        $this->merge([
            'countries' => CatalogCountries::uppercase($this->input('countries')),
        ]);
    }

    /** @return array<string, mixed> */
    protected function countryRules(): array
    {
        return [
            'countries' => ['nullable', 'array'],
            'countries.*' => ['string', 'size:2', Rule::in(CatalogCountries::codes())],
        ];
    }
}
