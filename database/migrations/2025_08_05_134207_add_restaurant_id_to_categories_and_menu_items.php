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
        // Add restaurant_id to categories table
        if (!Schema::hasColumn('categories', 'restaurant_id')) {
            Schema::table('categories', function (Blueprint $table) {
                $table->foreignId('restaurant_id')->after('id')->nullable()->constrained()->onDelete('cascade');
            });
        }
        
        // Add restaurant_id to menu_items table
        if (!Schema::hasColumn('menu_items', 'restaurant_id')) {
            Schema::table('menu_items', function (Blueprint $table) {
                $table->foreignId('restaurant_id')->after('id')->nullable()->constrained()->onDelete('cascade');
            });
        }
        
        // Set default restaurant_id for existing categories
        $restaurant = \App\Models\Restaurant::first();
        if ($restaurant) {
            \App\Models\Category::whereNull('restaurant_id')->update(['restaurant_id' => $restaurant->id]);
            \App\Models\MenuItem::whereNull('restaurant_id')->update(['restaurant_id' => $restaurant->id]);
        }
        
        // Make restaurant_id required after setting default values
        Schema::table('categories', function (Blueprint $table) {
            $table->foreignId('restaurant_id')->nullable(false)->change();
        });
        
        Schema::table('menu_items', function (Blueprint $table) {
            $table->foreignId('restaurant_id')->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropForeign(['restaurant_id']);
            $table->dropColumn('restaurant_id');
        });
        
        Schema::table('menu_items', function (Blueprint $table) {
            $table->dropForeign(['restaurant_id']);
            $table->dropColumn('restaurant_id');
        });
    }
};
