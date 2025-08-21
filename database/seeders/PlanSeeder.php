<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Plan;

class PlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create free plan for new users
        Plan::firstOrCreate(
            ['price' => 0.00],
            [
                'name' => 'Bepul Tarif',
                'description' => 'Yangi foydalanuvchilar uchun bepul tarif',
                'price' => 0.00,
                'duration_days' => 30,
                'limits' => [
                    'orders_per_month' => 50,
                    'menu_items' => 20,
                    'categories' => 5,
                    'couriers' => 2,
                ],
                'is_active' => true,
            ]
        );

        // Create basic paid plan
        Plan::firstOrCreate(
            ['price' => 29.99],
            [
                'name' => 'Asosiy Tarif',
                'description' => 'Kichik restoranlar uchun asosiy tarif',
                'price' => 29.99,
                'duration_days' => 30,
                'limits' => [
                    'orders_per_month' => 200,
                    'menu_items' => 100,
                    'categories' => 15,
                    'couriers' => 5,
                ],
                'is_active' => true,
            ]
        );

        // Create premium plan
        Plan::firstOrCreate(
            ['price' => 79.99],
            [
                'name' => 'Premium Tarif',
                'description' => 'Katta restoranlar uchun premium tarif',
                'price' => 79.99,
                'duration_days' => 30,
                'limits' => [
                    'orders_per_month' => 1000,
                    'menu_items' => 500,
                    'categories' => 50,
                    'couriers' => 20,
                ],
                'is_active' => true,
            ]
        );
    }
} 