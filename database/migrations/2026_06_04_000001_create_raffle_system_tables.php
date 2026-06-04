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
        // 1. Raffles table
        Schema::create('raffles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Organizer
            $table->string('title');
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2);
            $table->integer('total_numbers');
            $table->string('status')->default('active'); // active, paused, completed
            $table->string('prize_name');
            $table->text('prize_description')->nullable();
            $table->string('image_url')->nullable();
            $table->dateTime('draw_date');
            $table->string('live_url')->nullable();
            $table->timestamps();
        });

        // 2. Payments table
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->decimal('amount', 10, 2);
            $table->string('gateway')->default('asaas'); // asaas, mercadopago, etc.
            $table->string('gateway_transaction_id')->nullable()->unique();
            $table->string('status')->default('pending'); // pending, approved, refunded, failed
            $table->string('payment_method')->default('pix'); // pix, card, boleto
            $table->text('pix_qr_code')->nullable();
            $table->text('pix_qr_code_url')->nullable();
            $table->timestamps();
        });

        // 3. Tickets table
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('raffle_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('payment_id')->nullable()->constrained()->onDelete('set null');
            $table->integer('number');
            $table->string('status')->default('reserved'); // reserved, paid, cancelled
            $table->timestamps();

            $table->unique(['raffle_id', 'number']);
        });

        // 4. Draws table
        Schema::create('draws', function (Blueprint $table) {
            $table->id();
            $table->foreignId('raffle_id')->constrained()->onDelete('cascade');
            $table->integer('winning_number');
            $table->foreignId('winning_ticket_id')->nullable()->constrained('tickets')->onDelete('set null');
            $table->foreignId('winning_user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('live_url')->nullable();
            $table->dateTime('drawn_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('draws');
        Schema::dropIfExists('tickets');
        Schema::dropIfExists('payments');
        Schema::dropIfExists('raffles');
    }
};
