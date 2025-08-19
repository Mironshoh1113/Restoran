<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
	public function up(): void
	{
		Schema::create('subscriptions', function (Blueprint $table) {
			$table->id();
			$table->unsignedBigInteger('user_id');
			$table->unsignedBigInteger('restaurant_id');
			$table->unsignedBigInteger('plan_id');
			$table->timestamp('starts_at')->nullable();
			$table->timestamp('ends_at')->nullable();
			$table->enum('status', ['active', 'expired', 'cancelled'])->default('active');
			$table->json('limits_overrides')->nullable();
			$table->timestamps();

			$table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
			$table->foreign('restaurant_id')->references('id')->on('restaurants')->onDelete('cascade');
			$table->foreign('plan_id')->references('id')->on('plans')->onDelete('restrict');
			$table->index(['restaurant_id', 'status']);
		});
	}

	public function down(): void
	{
		Schema::dropIfExists('subscriptions');
	}
}; 