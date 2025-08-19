<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Plan extends Model
{
	use HasFactory;

	protected $fillable = [
		'name',
		'description',
		'price',
		'duration_days',
		'limits',
		'is_active',
	];

	protected $casts = [
		'price' => 'decimal:2',
		'duration_days' => 'integer',
		'limits' => 'array',
		'is_active' => 'boolean',
	];

	public function subscriptions(): HasMany
	{
		return $this->hasMany(Subscription::class);
	}
} 