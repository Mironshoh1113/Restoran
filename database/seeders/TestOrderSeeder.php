<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Order;
use App\Models\Restaurant;
use App\Models\Project;
use App\Models\Category;
use App\Models\MenuItem;
use App\Models\OrderItem;

class TestOrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get or create a restaurant
        $restaurant = Restaurant::first();
        if (!$restaurant) {
            $restaurant = Restaurant::create([
                'name' => 'Test Restaurant',
                'address' => 'Test Address',
                'phone' => '+998901234567',
                'bot_token' => 'test_bot_token_123',
            ]);
        }

        // Get or create a project
        $project = Project::where('restaurant_id', $restaurant->id)->first();
        if (!$project) {
            $project = Project::create([
                'name' => 'Test Project',
                'restaurant_id' => $restaurant->id,
            ]);
        }

        // Get or create a category
        $category = Category::where('project_id', $project->id)->first();
        if (!$category) {
            $category = Category::create([
                'name' => 'Test Category',
                'project_id' => $project->id,
            ]);
        }

        // Get or create menu items
        $menuItems = MenuItem::where('category_id', $category->id)->get();
        if ($menuItems->isEmpty()) {
            $menuItems = collect([
                MenuItem::create([
                    'name' => 'Test Item 1',
                    'description' => 'Test description 1',
                    'price' => 15000,
                    'category_id' => $category->id,
                ]),
                MenuItem::create([
                    'name' => 'Test Item 2',
                    'description' => 'Test description 2',
                    'price' => 25000,
                    'category_id' => $category->id,
                ]),
                MenuItem::create([
                    'name' => 'Test Item 3',
                    'description' => 'Test description 3',
                    'price' => 35000,
                    'category_id' => $category->id,
                ]),
            ]);
        }

        // Create test orders
        $testChatIds = [1238412611, 987654321, 555666777]; // Using actual user chat ID
        $statuses = ['new', 'preparing', 'on_way', 'delivered', 'cancelled'];

        foreach ($testChatIds as $index => $chatId) {
            // Create order
            $order = Order::create([
                'order_number' => 'ORD-' . str_pad(time() + $index, 6, '0', STR_PAD_LEFT),
                'restaurant_id' => $restaurant->id,
                'project_id' => $project->id,
                'telegram_chat_id' => $chatId,
                'customer_name' => 'Test Customer ' . ($index + 1),
                'customer_phone' => '+99890123456' . ($index + 1),
                'address' => 'Test Address ' . ($index + 1),
                'payment_type' => $index % 2 === 0 ? 'cash' : 'card',
                'status' => $statuses[$index % count($statuses)],
                'total_price' => 0
            ]);

            // Create order items
            $total = 0;
            foreach ($menuItems->take(2) as $menuItem) {
                $quantity = rand(1, 3);
                $subtotal = $menuItem->price * $quantity;
                $total += $subtotal;

                OrderItem::create([
                    'order_id' => $order->id,
                    'menu_item_id' => $menuItem->id,
                    'quantity' => $quantity,
                    'price' => $menuItem->price,
                ]);
            }

            // Update order total
            $order->update(['total_price' => $total]);
        }

        $this->command->info('Test orders created successfully!');
        $this->command->info('Test chat IDs: ' . implode(', ', $testChatIds));
    }
} 