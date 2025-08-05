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
            // Add missing columns for web interface
            $table->decimal('total_amount', 10, 2)->nullable()->after('total_price');
            $table->text('delivery_address')->nullable()->after('address');
            $table->string('payment_method')->nullable()->after('payment_type');
            $table->json('items')->nullable()->after('payment_method');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['total_amount', 'delivery_address', 'payment_method', 'items']);
        });
    }
};
