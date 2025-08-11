<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GlobalTelegramUser extends Model
{
    use HasFactory;

    protected $fillable = [
        'telegram_id',
        'username',
        'first_name',
        'last_name',
        'phone_number',
        'language_code',
        'is_bot',
        'is_active',
        'last_activity',
        'settings',
    ];

    protected $casts = [
        'is_bot' => 'boolean',
        'is_active' => 'boolean',
        'last_activity' => 'datetime',
        'settings' => 'array',
    ];

    /**
     * Get all restaurant-specific user records for this global user
     */
    public function restaurantUsers(): HasMany
    {
        return $this->hasMany(TelegramUser::class, 'telegram_id', 'telegram_id');
    }

    /**
     * Get all messages from this user across all restaurants
     */
    public function allMessages(): HasMany
    {
        return $this->hasMany(TelegramMessage::class, 'telegram_user_id', 'id');
    }

    /**
     * Get user's activity across all restaurants
     */
    public function getActivityAcrossRestaurants()
    {
        return $this->restaurantUsers()
            ->with(['restaurant', 'messages'])
            ->get()
            ->map(function ($restaurantUser) {
                return [
                    'restaurant' => $restaurantUser->restaurant,
                    'last_message' => $restaurantUser->messages()->latest()->first(),
                    'messages_count' => $restaurantUser->messages()->count(),
                    'last_activity' => $restaurantUser->last_activity,
                ];
            });
    }
} 