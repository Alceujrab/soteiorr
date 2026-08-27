<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('draws', function (Blueprint $table) {
            $table->string('status', 30)->default('pending')->after('raffle_id');
            $table->boolean('is_test')->default(false)->after('status');
            $table->unsignedTinyInteger('digit_length')->default(6)->after('is_test');
            $table->unsignedTinyInteger('revealed_digits')->default(0)->after('digit_length');
            $table->string('winning_number_padded', 12)->nullable()->after('winning_number');
            $table->json('winner_snapshot')->nullable()->after('live_url');
            $table->timestamp('started_at')->nullable()->after('winner_snapshot');
            $table->timestamp('completed_at')->nullable()->after('started_at');
            $table->timestamp('last_reveal_at')->nullable()->after('completed_at');
            $table->string('public_slug', 64)->nullable()->unique()->after('last_reveal_at');
        });
    }

    public function down(): void
    {
        Schema::table('draws', function (Blueprint $table) {
            $table->dropUnique(['public_slug']);
            $table->dropColumn([
                'status',
                'is_test',
                'digit_length',
                'revealed_digits',
                'winning_number_padded',
                'winner_snapshot',
                'started_at',
                'completed_at',
                'last_reveal_at',
                'public_slug',
            ]);
        });
    }
};
