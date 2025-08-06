<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class MenuItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'price',
        'category_id',
        'restaurant_id',
        'image',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Get the image URL
     */
    public function getImageUrlAttribute()
    {
        if ($this->image) {
            $url = Storage::url($this->image);
            \Log::info('Menu item image URL generated', [
                'item_id' => $this->id,
                'image_path' => $this->image,
                'image_url' => $url,
                'exists' => Storage::disk('public')->exists($this->image)
            ]);
            return $url;
        }
        return null;
    }

    /**
     * Check if item has image
     */
    public function hasImage()
    {
        if (empty($this->image)) {
            return false;
        }
        
        $exists = Storage::disk('public')->exists($this->image);
        \Log::info('Menu item image check', [
            'item_id' => $this->id,
            'image_path' => $this->image,
            'exists' => $exists
        ]);
        
        return $exists;
    }
} 