<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Restaurant extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'owner_user_id',
        'bot_token',
        'bot_username',
        'bot_name',
        'bot_description',
        'bot_image',
        'is_active',
        'phone',
        'address',
        'logo',
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
        'social_links',
        'web_app_title',
        'web_app_description',
        'web_app_button_text',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'delivery_fee' => 'decimal:2',
        'min_order_amount' => 'decimal:2',
        'payment_methods' => 'array',
        'social_links' => 'array',
    ];

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_user_id');
    }

    public function projects(): HasMany
    {
        return $this->hasMany(Project::class);
    }

    public function couriers(): HasMany
    {
        return $this->hasMany(Courier::class);
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function telegramUsers(): HasMany
    {
        return $this->hasMany(TelegramUser::class);
    }

    public function telegramMessages(): HasMany
    {
        return $this->hasMany(TelegramMessage::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    public function activeSubscription(): ?Subscription
    {
        return $this->subscriptions()
            ->where('status', 'active')
            ->where(function ($q) { $q->whereNull('ends_at')->orWhere('ends_at', '>=', now()); })
            ->latest('starts_at')
            ->with('plan')
            ->first();
    }
} 