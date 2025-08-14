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
            // Telegram Web App specific fields
            $table->string('web_app_title')->nullable()->after('bot_image');
            $table->text('web_app_description')->nullable()->after('web_app_title');
            $table->string('web_app_button_text')->nullable()->after('web_app_description');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('restaurants', function (Blueprint $table) {
            $table->dropColumn([
                'web_app_title',
                'web_app_description',
                'web_app_button_text'
            ]);
        });
    }
}; 