<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Activity Logs table for Audit & Compliance
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('action');
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->text('payload')->nullable(); // JSON or text description
            $table->timestamps();
        });

        // 2. Support Tickets table
        Schema::create('support_tickets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('subject');
            $table->string('category')->default('financeiro'); // financeiro, suporte_tecnico, duvidas
            $table->string('priority')->default('media'); // baixa, media, alta
            $table->text('message');
            $table->string('status')->default('aberto'); // aberto, respondido, fechado
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('support_tickets');
        Schema::dropIfExists('activity_logs');
    }
};
