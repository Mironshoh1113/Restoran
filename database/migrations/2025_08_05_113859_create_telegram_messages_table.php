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
        Schema::create('telegram_messages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('restaurant_id');
            $table->unsignedBigInteger('telegram_user_id');
            $table->bigInteger('message_id')->nullable();
            $table->enum('direction', ['incoming', 'outgoing']);
            $table->text('message_text');
            $table->json('message_data')->nullable(); // For storing additional message data
            $table->string('message_type')->default('text'); // text, photo, document, etc.
            $table->boolean('is_read')->default(false);
            $table->timestamps();
            
            $table->foreign('restaurant_id')->references('id')->on('restaurants')->onDelete('cascade');
            $table->foreign('telegram_user_id')->references('id')->on('telegram_users')->onDelete('cascade');
            $table->index(['restaurant_id', 'telegram_user_id', 'created_at'], 'telegram_messages_conversation_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('telegram_messages');
    }
};
