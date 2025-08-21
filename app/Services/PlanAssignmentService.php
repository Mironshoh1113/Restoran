<?php

namespace App\Services;

use App\Models\Plan;
use App\Models\Restaurant;
use App\Models\Subscription;
use App\Models\User;
use Carbon\Carbon;

class PlanAssignmentService
{
    /**
     * Assign free plan to a new user
     */
    public static function assignFreePlanToNewUser(User $user): ?Subscription
    {
        // Find the free plan (0.00 price)
        $freePlan = Plan::where('price', 0.00)
            ->where('is_active', true)
            ->first();

        if (!$freePlan) {
            return null;
        }

        // Get or create a default restaurant for the user
        $restaurantId = $user->restaurant_id;
        
        if (!$restaurantId) {
            // Find the first available restaurant or create a default one
            $restaurant = Restaurant::where('is_active', true)->first();
            
            if (!$restaurant) {
                // Create a default restaurant if none exists
                $restaurant = Restaurant::create([
                    'name' => 'Default Restaurant',
                    'owner_user_id' => $user->id,
                    'phone' => '+998000000000',
                    'address' => 'Default Address',
                    'is_active' => true,
                ]);
            }
            
            $restaurantId = $restaurant->id;
            
            // Update user with restaurant_id
            $user->update(['restaurant_id' => $restaurantId]);
        }

        // Create a subscription for the user
        $subscription = Subscription::create([
            'user_id' => $user->id,
            'restaurant_id' => $restaurantId,
            'plan_id' => $freePlan->id,
            'starts_at' => Carbon::now(),
            'ends_at' => Carbon::now()->addDays($freePlan->duration_days),
            'status' => 'active',
        ]);

        return $subscription;
    }

    /**
     * Check if user has an active subscription
     */
    public static function hasActiveSubscription(User $user): bool
    {
        return $user->subscriptions()
            ->where('status', 'active')
            ->where(function ($query) {
                $query->whereNull('ends_at')
                    ->orWhere('ends_at', '>=', Carbon::now());
            })
            ->exists();
    }

    /**
     * Get user's current active subscription
     */
    public static function getCurrentSubscription(User $user): ?Subscription
    {
        return $user->subscriptions()
            ->with('plan')
            ->where('status', 'active')
            ->where(function ($query) {
                $query->whereNull('ends_at')
                    ->orWhere('ends_at', '>=', Carbon::now());
            })
            ->latest('starts_at')
            ->first();
    }
} 