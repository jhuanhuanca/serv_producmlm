<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('compensation_plans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('network_type');
            $table->text('network_description')->nullable();
            $table->text('network_example')->nullable();
            $table->text('network_advantages')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('compensation_ranks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('compensation_plan_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->text('example')->nullable();
            $table->text('requirements')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('compensation_packages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('compensation_plan_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('pv', 10, 2)->default(0);
            $table->decimal('cost', 10, 2)->default(0);
            $table->string('image')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('compensation_bonuses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('compensation_plan_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->text('example')->nullable();
            $table->text('advantage')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('compensation_bonuses');
        Schema::dropIfExists('compensation_packages');
        Schema::dropIfExists('compensation_ranks');
        Schema::dropIfExists('compensation_plans');
    }
};
