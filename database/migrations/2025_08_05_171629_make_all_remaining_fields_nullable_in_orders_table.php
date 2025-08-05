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
        Schema::table('orders', function (Blueprint $table) {
            // Make all remaining fields nullable to prevent database errors
            $table->string('address')->nullable()->change();
            $table->string('payment_type')->nullable()->change();
            $table->string('notes')->nullable()->change();
            $table->foreignId('courier_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('address')->nullable(false)->change();
            $table->string('payment_type')->nullable(false)->change();
            $table->string('notes')->nullable(false)->change();
            $table->foreignId('courier_id')->nullable(false)->change();
        });
    }
};
