<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('raffle_packages', function (Blueprint $table) {
            $table->boolean('allows_selection')->default(false)->after('is_featured');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->string('affiliate_code', 32)->nullable()->unique()->after('role');
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->foreignId('affiliate_user_id')
                ->nullable()
                ->after('user_id')
                ->constrained('users')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropConstrainedForeignId('affiliate_user_id');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('affiliate_code');
        });

        Schema::table('raffle_packages', function (Blueprint $table) {
            $table->dropColumn('allows_selection');
        });
    }
};
