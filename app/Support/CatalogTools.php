<?php

declare(strict_types=1);

namespace App\Support;

final class CatalogTools
{
    /** @var list<string> */
    public const KEYS = [
        'wellness',
        'imc',
        'wellness_consult',
        'flyers',
        'pdfs',
        'videos',
        'audios',
        'ring_sizer',
    ];

    /**
     * null = todas (empresas antiguas). Array vacío = ninguna.
     *
     * @param  list<mixed>|null  $raw
     * @return list<string>
     */
    public static function normalize(?array $raw): array
    {
        if ($raw === null) {
            return self::KEYS;
        }

        $enabled = [];

        foreach ($raw as $item) {
            if (! is_string($item) || ! in_array($item, self::KEYS, true)) {
                continue;
            }

            if (! in_array($item, $enabled, true)) {
                $enabled[] = $item;
            }
        }

        return $enabled;
    }
}
