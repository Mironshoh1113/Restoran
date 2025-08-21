<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Plan;

class FreePlanSeeder extends Seeder
{
    public function run(): void
    {
        // Create free plan for new users
        Plan::firstOrCreate(
            ['price' => '0.00'],
            [
                'name' => 'Bepul',
                'description' => 'Yangi foydalanuvchilar uchun bepul tarif',
                'price' => '0.00',
                'duration_days' => 30,
                'is_active' => true,
                'limits' => [
                    'restaurants' => 1,
                    'categories' => 5,
                    'menu_items' => 50,
                    'orders' => 100,
                    'telegram_bots' => 1,
                ],
            ]
        );
    }
} 