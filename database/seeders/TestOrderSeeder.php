<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Order;
use App\Models\Restaurant;
use App\Models\User;
use Carbon\Carbon;

class TestOrderSeeder extends Seeder
{
    public function run(): void
    {
        // Get first restaurant
        $restaurant = Restaurant::first();
        if (!$restaurant) {
            $this->command->info('No restaurant found. Please run RestaurantSeeder first.');
            return;
        }

        // Get first user
        $user = User::first();
        if (!$user) {
            $this->command->info('No user found. Please run UserSeeder first.');
            return;
        }

        // Create test orders for the last 6 months
        $statuses = ['new', 'preparing', 'on_way', 'delivered', 'cancelled'];
        
        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $daysInMonth = $month->daysInMonth;
            
            // Create 3-8 orders per month
            $ordersCount = rand(3, 8);
            
            for ($j = 0; $j < $ordersCount; $j++) {
                $randomDay = rand(1, $daysInMonth);
                $orderDate = $month->copy()->day($randomDay)->hour(rand(8, 22))->minute(rand(0, 59));
                
                Order::create([
                    'order_number' => 'ORD-' . str_pad(Order::max('id') + 1, 6, '0', STR_PAD_LEFT),
                    'user_id' => $user->id,
                    'restaurant_id' => $restaurant->id,
                    'status' => $statuses[array_rand($statuses)],
                    'total_price' => rand(15000, 150000),
                    'payment_type' => rand(0, 1) ? 'cash' : 'card',
                    'address' => 'Test Address ' . rand(1, 100),
                    'customer_name' => 'Test Customer ' . rand(1, 50),
                    'customer_phone' => '+998' . rand(900000000, 999999999),
                    'total_amount' => rand(15000, 150000),
                    'delivery_address' => 'Test Delivery Address ' . rand(1, 100),
                    'payment_method' => rand(0, 1) ? 'cash' : 'card',
                    'items' => [
                        [
                            'name' => 'Test Item ' . rand(1, 10),
                            'price' => rand(5000, 50000),
                            'quantity' => rand(1, 5)
                        ]
                    ],
                    'created_at' => $orderDate,
                    'updated_at' => $orderDate
                ]);
            }
        }

        $this->command->info('Test orders created successfully!');
    }
} 