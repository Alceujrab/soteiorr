<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->date('birth_date')->nullable()->after('phone');
            $table->string('whatsapp', 20)->nullable()->after('birth_date');
            $table->string('phone_extra', 20)->nullable()->after('whatsapp');
            $table->string('zip_code', 12)->nullable()->after('phone_extra');
            $table->string('address_street')->nullable()->after('zip_code');
            $table->string('address_number', 30)->nullable()->after('address_street');
            $table->string('address_complement')->nullable()->after('address_number');
            $table->string('address_neighborhood')->nullable()->after('address_complement');
            $table->string('address_city')->nullable()->after('address_neighborhood');
            $table->string('address_state', 2)->nullable()->after('address_city');
            $table->string('asaas_customer_id')->nullable()->after('address_state');
            $table->timestamp('accepted_regulation_at')->nullable()->after('asaas_customer_id');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'birth_date',
                'whatsapp',
                'phone_extra',
                'zip_code',
                'address_street',
                'address_number',
                'address_complement',
                'address_neighborhood',
                'address_city',
                'address_state',
                'asaas_customer_id',
                'accepted_regulation_at',
            ]);
        });
    }
};
