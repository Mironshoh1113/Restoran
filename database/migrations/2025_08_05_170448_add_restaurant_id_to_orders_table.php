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
            $table->foreignId('restaurant_id')->after('id')->nullable()->constrained()->onDelete('cascade');
        });
        
        // Set default restaurant_id for existing orders
        $restaurant = \App\Models\Restaurant::first();
        if ($restaurant) {
            \App\Models\Order::whereNull('restaurant_id')->update(['restaurant_id' => $restaurant->id]);
        }
        
        // Make restaurant_id required after setting default values
        Schema::table('orders', function (Blueprint $table) {
            $table->foreignId('restaurant_id')->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['restaurant_id']);
            $table->dropColumn('restaurant_id');
        });
    }
};
