<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Product;
use App\Services\ProductService;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class ProductCountryTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::dropIfExists('products');
        Schema::create('products', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('company_id')->default(1);
            $table->unsignedBigInteger('category_id')->nullable();
            $table->string('code');
            $table->string('name');
            $table->string('image')->nullable();
            $table->text('description')->nullable();
            $table->longText('technical_sheet')->nullable();
            $table->decimal('price', 10, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->json('countries')->nullable();
            $table->timestamps();
        });
    }

    public function test_restricted_product_only_matches_selected_countries(): void
    {
        Product::query()->create([
            'company_id' => 1,
            'code' => 'VITA-ANDES',
            'name' => 'Vita Andes',
            'price' => 20,
            'countries' => ['BO', 'PE'],
        ]);

        $this->assertSame(['VITA-ANDES'], Product::query()->availableInCountry('BO')->pluck('code')->all());
        $this->assertSame(['VITA-ANDES'], Product::query()->availableInCountry('PE')->pluck('code')->all());
        $this->assertSame([], Product::query()->availableInCountry('MX')->pluck('code')->all());
    }

    public function test_product_without_countries_is_available_everywhere(): void
    {
        Product::query()->create([
            'company_id' => 1,
            'code' => 'ALL',
            'name' => 'Global',
            'price' => 10,
            'countries' => null,
        ]);

        $this->assertSame(['ALL'], Product::query()->availableInCountry('BO')->pluck('code')->all());
        $this->assertTrue(Product::query()->first()?->isAvailableIn('MX'));
    }

    public function test_product_service_filters_by_country_query(): void
    {
        Product::query()->create([
            'company_id' => 1,
            'code' => 'VITA-ANDES',
            'name' => 'Vita Andes',
            'price' => 20,
            'countries' => ['BO'],
        ]);
        Product::query()->create([
            'company_id' => 1,
            'code' => 'ALL',
            'name' => 'Global',
            'price' => 10,
            'countries' => null,
        ]);

        Schema::create('companies', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('slug')->nullable();
            $table->timestamps();
        });
        Schema::create('categories', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('company_id')->nullable();
            $table->string('name');
            $table->timestamps();
        });

        $page = app(ProductService::class)->paginate(Request::create('/products', 'GET', ['country' => 'MX']));
        $this->assertSame(['ALL'], $page->pluck('code')->all());
    }
}
