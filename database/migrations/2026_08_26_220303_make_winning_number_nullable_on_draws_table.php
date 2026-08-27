<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('draws', function (Blueprint $table) {
            $table->integer('winning_number')->nullable()->change();
            $table->timestamp('drawn_at')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('draws', function (Blueprint $table) {
            $table->integer('winning_number')->nullable(false)->change();
            $table->timestamp('drawn_at')->nullable(false)->change();
        });
    }
};
