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
        Schema::table('services', function (Blueprint $table) {
            $table->time('available_from')->nullable()->after('is_active');
            $table->time('available_to')->nullable()->after('available_from');
            $table->unsignedSmallInteger('slot_interval_minutes')->nullable()->after('available_to');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->dropColumn(['available_from', 'available_to', 'slot_interval_minutes']);
        });
    }
};
