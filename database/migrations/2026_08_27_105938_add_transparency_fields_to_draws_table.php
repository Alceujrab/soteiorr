<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('draws', function (Blueprint $table) {
            $table->string('draw_seed', 64)->nullable()->after('winner_snapshot');
            $table->string('eligible_hash', 64)->nullable()->after('draw_seed');
            $table->unsignedInteger('eligible_count')->nullable()->after('eligible_hash');
            $table->unsignedInteger('selection_index')->nullable()->after('eligible_count');
            $table->json('eligible_numbers')->nullable()->after('selection_index');
            $table->json('ops_checklist')->nullable()->after('eligible_numbers');
        });
    }

    public function down(): void
    {
        Schema::table('draws', function (Blueprint $table) {
            $table->dropColumn([
                'draw_seed',
                'eligible_hash',
                'eligible_count',
                'selection_index',
                'eligible_numbers',
                'ops_checklist',
            ]);
        });
    }
};
