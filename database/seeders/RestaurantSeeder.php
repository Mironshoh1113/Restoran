<?php

namespace Database\Seeders;

use App\Models\Restaurant;
use App\Models\User;
use Illuminate\Database\Seeder;

class RestaurantSeeder extends Seeder
{
    public function run(): void
    {
        $manager = User::where('role', 'restaurant_manager')->first();

        Restaurant::create([
            'name' => 'Oshxona Restaurant',
            'owner_user_id' => $manager->id,
            'phone' => '+998901234567',
            'address' => 'Toshkent shahri, Chilonzor tumani, 1-uy',
            'bot_token' => 'your_bot_token_here',
            'bot_username' => 'your_bot_username',
            'is_active' => true,
        ]);

        Restaurant::create([
            'name' => 'Pizza House',
            'owner_user_id' => $manager->id,
            'phone' => '+998901234568',
            'address' => 'Toshkent shahri, Yunusobod tumani, 15-uy',
            'bot_token' => 'your_bot_token_here',
            'bot_username' => 'your_bot_username',
            'is_active' => true,
        ]);
    }
} 