<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('draws', function (Blueprint $table) {
            $table->timestamp('auto_reveal_started_at')->nullable()->after('last_reveal_at');
        });
    }

    public function down(): void
    {
        Schema::table('draws', function (Blueprint $table) {
            $table->dropColumn('auto_reveal_started_at');
        });
    }
};
