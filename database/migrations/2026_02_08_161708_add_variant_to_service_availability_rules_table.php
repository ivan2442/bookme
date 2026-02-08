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
        Schema::table('service_variants', function (Blueprint $table) {
            $table->boolean('is_special')->default(false)->after('is_active');
        });

        Schema::table('service_availability_rules', function (Blueprint $table) {
            $table->foreignId('service_variant_id')->nullable()->after('service_id')->constrained()->cascadeOnDelete();
            $table->foreignId('service_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('service_availability_rules', function (Blueprint $table) {
            $table->dropForeign(['service_variant_id']);
            $table->dropColumn('service_variant_id');
            $table->foreignId('service_id')->nullable(false)->change();
        });

        Schema::table('service_variants', function (Blueprint $table) {
            $table->dropColumn('is_special');
        });
    }
};
