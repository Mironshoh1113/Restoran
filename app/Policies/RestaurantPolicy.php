<?php

namespace App\Policies;

use App\Models\Restaurant;
use App\Models\User;

class RestaurantPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isSuperAdmin() || $user->isRestaurantManager();
    }

    public function view(User $user, Restaurant $restaurant): bool
    {
        return $user->isSuperAdmin() || $user->id === $restaurant->owner_user_id;
    }

    public function create(User $user): bool
    {
        return $user->isSuperAdmin() || $user->isRestaurantManager();
    }

    public function update(User $user, Restaurant $restaurant): bool
    {
        return $user->isSuperAdmin() || $user->id === $restaurant->owner_user_id;
    }

    public function delete(User $user, Restaurant $restaurant): bool
    {
        return $user->isSuperAdmin() || $user->id === $restaurant->owner_user_id;
    }
} 