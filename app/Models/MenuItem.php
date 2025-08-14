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
            // Support both old and new storage systems
            if (strpos($this->image, 'img/menu/') === 0) {
                // Old system: direct public path
                $url = asset($this->image);
                $fullPath = public_path($this->image);
                $exists = file_exists($fullPath);
            } else {
                // New system: Laravel Storage
                $url = asset('storage/' . $this->image);
                $fullPath = storage_path('app/public/' . $this->image);
                $exists = \Storage::disk('public')->exists($this->image);
            }
            
            \Log::info('Menu item image URL generated', [
                'item_id' => $this->id,
                'image_path' => $this->image,
                'image_url' => $url,
                'full_path' => $fullPath,
                'exists' => $exists,
                'storage_system' => strpos($this->image, 'img/menu/') === 0 ? 'old' : 'new'
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
        
        // Support both old and new storage systems
        if (strpos($this->image, 'img/menu/') === 0) {
            // Old system: direct public path
            $fullPath = public_path($this->image);
            $exists = file_exists($fullPath);
            $fileSize = $exists ? filesize($fullPath) : 0;
            $isValid = $exists && $fileSize > 0;
        } else {
            // New system: Laravel Storage
            $exists = \Storage::disk('public')->exists($this->image);
            $fileSize = $exists ? \Storage::disk('public')->size($this->image) : 0;
            $isValid = $exists && $fileSize > 0;
            $fullPath = storage_path('app/public/' . $this->image);
        }
        
        \Log::info('Menu item image check', [
            'item_id' => $this->id,
            'image_path' => $this->image,
            'full_path' => $fullPath,
            'exists' => $exists,
            'file_size' => $fileSize,
            'is_valid' => $isValid,
            'storage_system' => strpos($this->image, 'img/menu/') === 0 ? 'old' : 'new'
        ]);
        
        return $isValid;
    }
} 