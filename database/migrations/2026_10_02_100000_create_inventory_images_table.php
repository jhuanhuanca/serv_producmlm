<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventory_images', function (Blueprint $table): void {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->unsignedBigInteger('company_id')->nullable()->index();
            $table->string('owner_key', 80)->nullable()->index();
            $table->string('kind', 20);
            $table->string('original_name');
            $table->string('mime', 80);
            $table->unsignedInteger('size')->default(0);
            $table->longBlob('content');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_images');
    }
};
