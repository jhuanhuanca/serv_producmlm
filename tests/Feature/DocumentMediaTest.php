<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Document;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class DocumentMediaTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::dropIfExists('documents');
        Schema::dropIfExists('companies');

        Schema::create('companies', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('documents', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('company_id');
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('file_path', 2048);
            $table->string('file_type', 20);
            $table->string('original_name')->nullable();
            $table->string('thumbnail', 2048)->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function test_admin_can_register_video_pdf_and_playable_audio(): void
    {
        $company = Company::query()->create([
            'name' => 'HGW',
            'is_active' => true,
        ]);

        $this->withServiceToken()
            ->postJson("/api/v1/companies/{$company->id}/documents", [
                'title' => 'Capacitación de cierre',
                'url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                'file_type' => 'video',
                'description' => 'Video de la empresa',
            ])
            ->assertCreated()
            ->assertJsonPath('data.file_type', 'video')
            ->assertJsonPath('data.player', 'iframe')
            ->assertJsonPath('data.embed_url', 'https://www.youtube.com/embed/dQw4w9WgXcQ')
            ->assertJsonPath('data.kind', 'video');

        $this->withServiceToken()
            ->postJson("/api/v1/companies/{$company->id}/documents", [
                'title' => 'Flyer PDF',
                'file_path' => 'https://ejemplo.com/flyer.pdf',
                'file_type' => 'pdf',
            ])
            ->assertCreated()
            ->assertJsonPath('data.kind', 'pdf')
            ->assertJsonPath('data.player', 'pdf');

        $this->withServiceToken()
            ->postJson("/api/v1/companies/{$company->id}/documents", [
                'title' => 'Audio de bienvenida',
                'url' => 'https://ejemplo.com/audio/bienvenida.mp3',
                'file_type' => 'audio',
            ])
            ->assertCreated()
            ->assertJsonPath('data.player', 'audio')
            ->assertJsonPath('data.url', 'https://ejemplo.com/audio/bienvenida.mp3');

        $this->withServiceToken()
            ->getJson("/api/v1/companies/{$company->id}/documents?kind=audio&active_only=1")
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.file_type', 'audio');

        $this->assertSame(3, Document::query()->count());
    }

    public function test_inactive_documents_are_hidden_from_active_listing(): void
    {
        $company = Company::query()->create([
            'name' => 'Scentia',
            'is_active' => true,
        ]);
        $hidden = Document::query()->create([
            'company_id' => $company->id,
            'title' => 'Borrador',
            'file_path' => 'https://ejemplo.com/a.mp3',
            'file_type' => Document::TYPE_AUDIO,
            'is_active' => false,
        ]);

        $this->withServiceToken()
            ->getJson("/api/v1/companies/{$company->id}/documents?active_only=1")
            ->assertOk()
            ->assertJsonCount(0, 'data');

        $this->withServiceToken()
            ->putJson("/api/v1/documents/{$hidden->id}", ['is_active' => true])
            ->assertOk()
            ->assertJsonPath('data.is_active', true);
    }

    public function test_payment_vouchers_are_registered_but_hidden_from_library(): void
    {
        $company = Company::query()->create([
            'name' => 'FWP',
            'is_active' => true,
        ]);

        $this->withServiceToken()
            ->postJson("/api/v1/companies/{$company->id}/documents", [
                'title' => 'Comprobante orden 12',
                'url' => 'https://rexmlm.test/storage/vouchers/1.jpg',
                'file_type' => 'voucher',
                'description' => 'Tienda 1 · QR Binance',
            ])
            ->assertCreated()
            ->assertJsonPath('data.file_type', 'voucher')
            ->assertJsonPath('data.kind', 'voucher')
            ->assertJsonPath('data.player', 'image');

        $this->withServiceToken()
            ->getJson("/api/v1/companies/{$company->id}/documents")
            ->assertOk()
            ->assertJsonCount(0, 'data');

        $this->withServiceToken()
            ->getJson("/api/v1/companies/{$company->id}/documents?file_type=voucher")
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.file_type', 'voucher');
    }

    private function withServiceToken(): static
    {
        return $this->withHeaders(['X-Service-Token' => 'test-token']);
    }
}
