<?php

use App\Models\Setting;
use App\Support\DefaultRegulationContent;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Setting::set('page_regulation', DefaultRegulationContent::html());
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Setting::query()->where('key', 'page_regulation')->delete();
    }
};
