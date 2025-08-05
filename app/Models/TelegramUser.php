<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TelegramUser extends Model
{
    use HasFactory;

    protected $fillable = [
        'restaurant_id',
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

    public function restaurant(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(TelegramMessage::class);
    }

    /**
     * Get unread messages count
     */
    public function getUnreadMessagesCountAttribute(): int
    {
        return $this->messages()->unread()->count();
    }

    /**
     * Get last message
     */
    public function getLastMessageAttribute()
    {
        return $this->messages()->latest()->first();
    }

    /**
     * Get full name
     */
    public function getFullNameAttribute(): string
    {
        $name = $this->first_name ?? '';
        if ($this->last_name) {
            $name .= ' ' . $this->last_name;
        }
        return trim($name) ?: 'Noma\'lum foydalanuvchi';
    }

    /**
     * Get display name
     */
    public function getDisplayNameAttribute(): string
    {
        if ($this->username) {
            return '@' . $this->username;
        }
        return $this->full_name;
    }

    /**
     * Scope for active users
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for recent activity
     */
    public function scopeRecent($query, $days = 30)
    {
        return $query->where('last_activity', '>=', now()->subDays($days));
    }

    /**
     * Update last activity
     */
    public function updateActivity(): void
    {
        $this->update(['last_activity' => now()]);
    }

    /**
     * Get user settings
     */
    public function getSetting(string $key, $default = null)
    {
        return $this->settings[$key] ?? $default;
    }

    /**
     * Set user setting
     */
    public function setSetting(string $key, $value): void
    {
        $settings = $this->settings ?? [];
        $settings[$key] = $value;
        $this->update(['settings' => $settings]);
    }
}
