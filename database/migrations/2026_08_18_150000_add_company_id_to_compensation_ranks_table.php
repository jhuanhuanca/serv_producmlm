<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('compensation_ranks', function (Blueprint $table) {
            $table->foreignId('company_id')
                ->nullable()
                ->after('id')
                ->constrained()
                ->cascadeOnDelete();
        });

        DB::statement('
            UPDATE compensation_ranks AS ranks
            INNER JOIN compensation_plans AS plans ON plans.id = ranks.compensation_plan_id
            SET ranks.company_id = plans.company_id
            WHERE ranks.company_id IS NULL
        ');

        Schema::table('compensation_ranks', function (Blueprint $table) {
            $table->dropForeign(['compensation_plan_id']);
        });

        Schema::table('compensation_ranks', function (Blueprint $table) {
            $table->unsignedBigInteger('compensation_plan_id')->nullable()->change();
            $table->foreign('compensation_plan_id')
                ->references('id')
                ->on('compensation_plans')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('compensation_ranks', function (Blueprint $table) {
            $table->dropForeign(['company_id']);
            $table->dropColumn('company_id');
            $table->dropForeign(['compensation_plan_id']);
        });

        Schema::table('compensation_ranks', function (Blueprint $table) {
            $table->unsignedBigInteger('compensation_plan_id')->nullable(false)->change();
            $table->foreign('compensation_plan_id')
                ->references('id')
                ->on('compensation_plans')
                ->cascadeOnDelete();
        });
    }
};
