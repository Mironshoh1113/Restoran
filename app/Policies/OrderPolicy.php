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
            return $order->project->restaurant->owner_user_id === $user->id;
        }

        return false;
    }

    public function update(User $user, Order $order): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        if ($user->isRestaurantManager()) {
            return $order->project->restaurant->owner_user_id === $user->id;
        }

        return false;
    }
} 