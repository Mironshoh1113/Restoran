<?php

namespace Database\Seeders;

use App\Models\Courier;
use App\Models\Restaurant;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class CourierSeeder extends Seeder
{
    public function run(): void
    {
        $restaurants = Restaurant::all();

        foreach ($restaurants as $restaurant) {
            Courier::create([
                'name' => 'Kuryer ' . $restaurant->name,
                'phone' => '+998901234570',
                'password' => Hash::make('password'),
                'restaurant_id' => $restaurant->id,
                'is_active' => true,
            ]);

            Courier::create([
                'name' => 'Kuryer 2 ' . $restaurant->name,
                'phone' => '+998901234571',
                'password' => Hash::make('password'),
                'restaurant_id' => $restaurant->id,
                'is_active' => true,
            ]);
        }
    }
} 