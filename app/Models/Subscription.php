<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

class Subscription extends Model
{
	use HasFactory;

	protected $fillable = [
		'user_id',
		'restaurant_id',
		'plan_id',
		'starts_at',
		'ends_at',
		'status',
		'limits_overrides',
	];

	protected $casts = [
		'starts_at' => 'datetime',
		'ends_at' => 'datetime',
		'limits_overrides' => 'array',
	];

	public function user(): BelongsTo { return $this->belongsTo(User::class); }
	public function restaurant(): BelongsTo { return $this->belongsTo(Restaurant::class); }
	public function plan(): BelongsTo { return $this->belongsTo(Plan::class); }

	public function isActive(): bool
	{
		if ($this->status !== 'active') return false;
		if (!$this->ends_at) return true;
		return now()->lte($this->ends_at);
	}

	public function effectiveLimits(): array
	{
		$planLimits = $this->plan?->limits ?? [];
		return array_merge($planLimits, $this->limits_overrides ?? []);
	}
} 