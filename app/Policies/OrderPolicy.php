<?php

namespace App\Policies;

use App\Models\Order;
use App\Models\User;

class OrderPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isSuperAdmin() || $user->isRestaurantManager();
    }

    public function view(User $user, Order $order): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        if ($user->isRestaurantManager()) {
            // Web interface dan kelgan buyurtmalar uchun restaurant_id ishlatamiz
            if ($order->restaurant_id && $order->restaurant) {
                return $order->restaurant->owner_user_id === $user->id;
            }
            
            // Eski buyurtmalar uchun project orqali tekshiramiz
            if ($order->project && $order->project->restaurant) {
                return $order->project->restaurant->owner_user_id === $user->id;
            }
        }

        return false;
    }

    public function update(User $user, Order $order): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        if ($user->isRestaurantManager()) {
            // Web interface dan kelgan buyurtmalar uchun restaurant_id ishlatamiz
            if ($order->restaurant_id && $order->restaurant) {
                return $order->restaurant->owner_user_id === $user->id;
            }
            
            // Eski buyurtmalar uchun project orqali tekshiramiz
            if ($order->project && $order->project->restaurant) {
                return $order->project->restaurant->owner_user_id === $user->id;
            }
        }

        return false;
    }
} 