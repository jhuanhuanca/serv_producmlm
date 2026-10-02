<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\InventoryImage;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class InventoryImageTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::dropIfExists('inventory_images');
        Schema::create('inventory_images', function (Blueprint $table): void {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->unsignedBigInteger('company_id')->nullable();
            $table->string('owner_key', 80)->nullable();
            $table->string('kind', 20);
            $table->string('original_name');
            $table->string('mime', 80);
            $table->unsignedInteger('size')->default(0);
            $table->binary('content');
            $table->timestamps();
        });
    }

    public function test_service_can_store_image_bytes_in_the_database(): void
    {
        $file = UploadedFile::fake()->image('te.jpg', 40, 40);

        $this->withHeaders(['X-Service-Token' => 'test-token'])
            ->post('/api/v1/inventory-images', [
                'kind' => 'personal',
                'company_id' => 7,
                'owner_key' => 'store:12',
                'file' => $file,
            ])
            ->assertCreated()
            ->assertJsonPath('data.kind', 'personal')
            ->assertJsonMissingPath('data.content');

        $row = InventoryImage::query()->first();
        $this->assertNotNull($row);
        $this->assertNotEmpty($row->content);
        $this->assertSame('personal', $row->kind);
        $this->assertSame(7, (int) $row->company_id);
    }

    public function test_stored_image_is_served_by_uuid(): void
    {
        $row = InventoryImage::query()->create([
            'uuid' => 'aaaaaaaa-bbbb-4ccc-8ddd-eeeeeeeeeeee',
            'kind' => 'incentive',
            'original_name' => 'regalo.png',
            'mime' => 'image/png',
            'size' => 4,
            'content' => 'PNG!',
        ]);

        $this->get('/api/v1/inventory-images/'.$row->uuid)
            ->assertOk()
            ->assertHeader('Content-Type', 'image/png')
            ->assertSee('PNG!', false);
    }

    public function test_upload_requires_service_token(): void
    {
        $this->post('/api/v1/inventory-images', [
            'kind' => 'personal',
            'file' => UploadedFile::fake()->image('x.jpg'),
        ])->assertUnauthorized();
    }
}
