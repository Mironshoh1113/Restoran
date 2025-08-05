<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Restaurant;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TestUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create test restaurant
        $restaurant = Restaurant::create([
            'name' => 'Test Restoran',
            'owner_user_id' => 1, // Will be created below
            'phone' => '+998901234567',
            'address' => 'Toshkent shahri, Test ko\'chasi, 123-uy',
            'bot_token' => 'test_bot_token_123',
            'bot_username' => 'test_restaurant_bot',
            'is_active' => true,
        ]);

        // Create test user (Super Admin)
        User::create([
            'name' => 'Test Admin',
            'email' => 'a@a.a',
            'password' => Hash::make('aaaa'),
            'role' => 'super_admin',
            'phone' => '+998901234567',
        ]);

        // Create test restaurant manager
        User::create([
            'name' => 'Test Manager',
            'email' => 'manager@test.com',
            'password' => Hash::make('password'),
            'role' => 'restaurant_manager',
            'restaurant_id' => $restaurant->id,
            'phone' => '+998901234568',
        ]);

        // Create test courier
        User::create([
            'name' => 'Test Courier',
            'email' => 'courier@test.com',
            'password' => Hash::make('password'),
            'role' => 'courier',
            'restaurant_id' => $restaurant->id,
            'phone' => '+998901234569',
        ]);

        // Create test project
        $project = \App\Models\Project::create([
            'name' => 'Asosiy menyu',
            'restaurant_id' => $restaurant->id,
            'description' => 'Test restoranning asosiy menyusi',
            'is_active' => true,
            'sort_order' => 0,
        ]);

        // Create test categories
        $categories = [
            [
                'name' => 'Hot dishes',
                'description' => 'Issiq taomlar',
                'sort_order' => 0,
            ],
            [
                'name' => 'Cold dishes',
                'description' => 'Sovuq taomlar',
                'sort_order' => 1,
            ],
            [
                'name' => 'Drinks',
                'description' => 'Ichimliklar',
                'sort_order' => 2,
            ],
        ];

        foreach ($categories as $categoryData) {
            $category = \App\Models\Category::create([
                'name' => $categoryData['name'],
                'project_id' => $project->id,
                'description' => $categoryData['description'],
                'sort_order' => $categoryData['sort_order'],
                'is_active' => true,
            ]);

            // Create test menu items for each category
            $this->createTestMenuItems($category);
        }
    }

    private function createTestMenuItems($category)
    {
        $items = [];

        switch ($category->name) {
            case 'Hot dishes':
                $items = [
                    ['name' => 'Plov', 'price' => 25000, 'description' => 'Osh plov'],
                    ['name' => 'Shashlik', 'price' => 15000, 'description' => 'Qiyma shashlik'],
                    ['name' => 'Lagman', 'price' => 20000, 'description' => 'Issiq lagman'],
                ];
                break;
            case 'Cold dishes':
                $items = [
                    ['name' => 'Salat', 'price' => 8000, 'description' => 'Chorva salat'],
                    ['name' => 'Achchiq chuchvara', 'price' => 12000, 'description' => 'Sovuq chuchvara'],
                ];
                break;
            case 'Drinks':
                $items = [
                    ['name' => 'Choy', 'price' => 2000, 'description' => 'Qora choy'],
                    ['name' => 'Kola', 'price' => 5000, 'description' => 'Coca Cola'],
                    ['name' => 'Suv', 'price' => 1000, 'description' => 'Mineral suv'],
                ];
                break;
        }

        foreach ($items as $itemData) {
            \App\Models\MenuItem::create([
                'name' => $itemData['name'],
                'category_id' => $category->id,
                'price' => $itemData['price'],
                'description' => $itemData['description'],
                'is_active' => true,
                'sort_order' => 0,
            ]);
        }
    }
} 