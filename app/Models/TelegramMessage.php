<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TelegramMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'restaurant_id',
        'telegram_user_id',
        'message_id',
        'direction',
        'message_text',
        'message_data',
        'message_type',
        'is_read',
    ];

    protected $casts = [
        'message_data' => 'array',
        'is_read' => 'boolean',
    ];

    public function restaurant(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function telegramUser(): BelongsTo
    {
        return $this->belongsTo(TelegramUser::class);
    }

    /**
     * Scope for incoming messages
     */
    public function scopeIncoming($query)
    {
        return $query->where('direction', 'incoming');
    }

    /**
     * Scope for outgoing messages
     */
    public function scopeOutgoing($query)
    {
        return $query->where('direction', 'outgoing');
    }

    /**
     * Scope for unread messages
     */
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    /**
     * Mark message as read
     */
    public function markAsRead(): void
    {
        $this->update(['is_read' => true]);
    }
}
