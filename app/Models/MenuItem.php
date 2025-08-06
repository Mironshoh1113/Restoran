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
            $fullPath = storage_path('app/public/' . $this->image);
            
            \Log::info('Menu item image URL generated', [
                'item_id' => $this->id,
                'image_path' => $this->image,
                'image_url' => $url,
                'full_path' => $fullPath,
                'exists' => file_exists($fullPath),
                'file_size' => file_exists($fullPath) ? filesize($fullPath) : 0
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
        
        $fullPath = storage_path('app/public/' . $this->image);
        $exists = file_exists($fullPath);
        $fileSize = $exists ? filesize($fullPath) : 0;
        
        \Log::info('Menu item image check', [
            'item_id' => $this->id,
            'image_path' => $this->image,
            'full_path' => $fullPath,
            'exists' => $exists,
            'file_size' => $fileSize,
            'is_valid' => $exists && $fileSize > 0
        ]);
        
        return $exists && $fileSize > 0;
    }
} 