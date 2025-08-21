<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContactMessage extends Model
{
	use HasFactory;

	protected $fillable = [
		'name',
		'email',
		'message',
		'status',
		'ip_address',
		'user_agent',
		'responded_at',
	];

	protected $casts = [
		'responded_at' => 'datetime',
	];
} 