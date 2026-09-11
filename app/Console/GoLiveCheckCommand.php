<?php

declare(strict_types=1);

namespace App\Console;

use Illuminate\Console\Command;

class GoLiveCheckCommand extends Command
{
    protected $signature = 'catalog:go-live-check {--as-production}';

    protected $description = 'Comprueba debug, HTTPS y SERVICE_TOKEN del catálogo';

    public function handle(): int
    {
        $production = $this->option('as-production') || $this->laravel->environment('production');
        $fails = [];

        if ($production && (bool) config('app.debug')) {
            $fails[] = 'APP_DEBUG debe ser false.';
        }

        if ($production && ! str_starts_with((string) config('app.url'), 'https://')) {
            $fails[] = 'APP_URL debe ser https://…';
        }

        $token = trim((string) config('catalog.service_token'));
        if ($production && (strlen($token) < 32 || in_array(strtolower($token), ['change-me', 'change-me-local-catalog', 'secret'], true))) {
            $fails[] = 'SERVICE_TOKEN debe tener ≥32 caracteres y ser distinto del ejemplo.';
        }

        if ($fails === []) {
            $this->info('Catálogo: configuración de go-live OK.');

            return self::SUCCESS;
        }

        foreach ($fails as $fail) {
            $this->error($fail);
        }

        return self::FAILURE;
    }
}
