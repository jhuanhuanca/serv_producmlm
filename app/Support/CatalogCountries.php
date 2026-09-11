<?php

declare(strict_types=1);

namespace App\Support;

final class CatalogCountries
{
    /** @return list<string> */
    public static function codes(): array
    {
        $countries = config('catalog.countries', []);

        if (! is_array($countries)) {
            return [];
        }

        $codes = [];

        foreach ($countries as $country) {
            if (! is_array($country)) {
                continue;
            }

            $code = strtoupper(trim((string) ($country['code'] ?? '')));

            if ($code !== '') {
                $codes[] = $code;
            }
        }

        return array_values(array_unique($codes));
    }

    /**
     * @param  mixed  $raw
     * @return list<string>
     */
    public static function uppercase(mixed $raw): array
    {
        if (! is_array($raw)) {
            return [];
        }

        $codes = [];

        foreach ($raw as $item) {
            $code = strtoupper(trim((string) $item));

            if ($code !== '') {
                $codes[] = $code;
            }
        }

        return array_values(array_unique($codes));
    }
}
