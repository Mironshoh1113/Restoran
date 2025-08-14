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
        Schema::table('restaurants', function (Blueprint $table) {
            // Color customization fields
            $table->string('primary_color')->default('#667eea')->after('logo');
            $table->string('secondary_color')->default('#764ba2')->after('primary_color');
            $table->string('accent_color')->default('#ff6b35')->after('secondary_color');
            $table->string('text_color')->default('#2c3e50')->after('accent_color');
            $table->string('bg_color')->default('#f8f9fa')->after('text_color');
            $table->string('card_bg')->default('#ffffff')->after('bg_color');
            $table->string('border_radius')->default('16px')->after('card_bg');
            $table->string('shadow')->default('0 8px 32px rgba(0,0,0,0.1)')->after('border_radius');
            
            // Business fields
            $table->text('description')->nullable()->after('shadow');
            $table->string('working_hours')->nullable()->after('description');
            $table->decimal('delivery_fee', 10, 2)->default(0)->after('working_hours');
            $table->decimal('min_order_amount', 10, 2)->default(0)->after('delivery_fee');
            $table->json('payment_methods')->nullable()->after('min_order_amount');
            $table->json('social_links')->nullable()->after('payment_methods');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('restaurants', function (Blueprint $table) {
            $table->dropColumn([
                'primary_color',
                'secondary_color',
                'accent_color',
                'text_color',
                'bg_color',
                'card_bg',
                'border_radius',
                'shadow',
                'description',
                'working_hours',
                'delivery_fee',
                'min_order_amount',
                'payment_methods',
                'social_links'
            ]);
        });
    }
}; 