<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Company;
use App\Support\CatalogTools;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class CompanyEnabledToolsTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::dropIfExists('companies');
        Schema::create('companies', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('logo')->nullable();
            $table->json('color_palette')->nullable();
            $table->string('website')->nullable();
            $table->boolean('is_active')->default(true);
            $table->json('enabled_tools')->nullable();
            $table->timestamps();
        });
    }

    public function test_new_company_exposes_all_tools_until_admin_restricts_them(): void
    {
        $company = Company::query()->create([
            'name' => 'Scentia',
            'is_active' => true,
        ]);

        $this->assertSame(CatalogTools::KEYS, $company->resolvedEnabledTools());
    }

    public function test_admin_can_restrict_tools_for_a_company(): void
    {
        $company = Company::query()->create([
            'name' => 'FWP',
            'is_active' => true,
        ]);

        $this->withHeaders(['X-Service-Token' => 'test-token'])
            ->putJson("/api/v1/companies/{$company->id}", [
                'enabled_tools' => ['imc', 'flyers'],
            ])
            ->assertOk()
            ->assertJsonPath('data.enabled_tools', ['imc', 'flyers']);
    }
}
