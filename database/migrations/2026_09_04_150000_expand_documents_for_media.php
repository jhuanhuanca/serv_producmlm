<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->text('description')->nullable()->after('title');
            $table->string('thumbnail', 2048)->nullable()->after('original_name');
            $table->unsignedSmallInteger('sort_order')->default(0)->after('thumbnail');
            $table->boolean('is_active')->default(true)->after('sort_order');
        });
    }

    public function down(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->dropColumn(['description', 'thumbnail', 'sort_order', 'is_active']);
        });
    }
};
