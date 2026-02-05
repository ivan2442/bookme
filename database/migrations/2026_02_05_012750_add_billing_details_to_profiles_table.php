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
        Schema::table('profiles', function (Blueprint $table) {
            $table->string('billing_name')->nullable();
            $table->string('billing_address')->nullable();
            $table->string('billing_city')->nullable();
            $table->string('billing_postal_code')->nullable();
            $table->string('billing_country')->nullable();
            $table->string('billing_ico')->nullable();
            $table->string('billing_dic')->nullable();
            $table->string('billing_ic_dph')->nullable();
            $table->string('billing_iban')->nullable();
            $table->string('billing_swift')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('profiles', function (Blueprint $table) {
            $table->dropColumn([
                'billing_name',
                'billing_address',
                'billing_city',
                'billing_postal_code',
                'billing_country',
                'billing_ico',
                'billing_dic',
                'billing_ic_dph',
                'billing_iban',
                'billing_swift',
            ]);
        });
    }
};
