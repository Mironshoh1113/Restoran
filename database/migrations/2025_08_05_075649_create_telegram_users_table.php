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
        Schema::create('telegram_users', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('restaurant_id');
            $table->bigInteger('telegram_id');
            $table->string('username')->nullable();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('phone_number')->nullable();
            $table->string('language_code')->default('uz');
            $table->boolean('is_bot')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_activity')->nullable();
            $table->json('settings')->nullable(); // For storing user preferences
            $table->timestamps();
            
            $table->foreign('restaurant_id')->references('id')->on('restaurants')->onDelete('cascade');
            
            // Composite unique index to ensure one user per restaurant
            $table->unique(['restaurant_id', 'telegram_id']);
            
            // Indexes for better performance
            $table->index(['restaurant_id', 'is_active']);
            $table->index(['restaurant_id', 'last_activity']);
            $table->index(['telegram_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('telegram_users');
    }
};
