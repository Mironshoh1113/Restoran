<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\MenuItem;
use Illuminate\Database\Seeder;

class MenuItemSeeder extends Seeder
{
    public function run(): void
    {
        $categories = Category::all();

        foreach ($categories as $category) {
            $menuItems = $this->getMenuItemsByCategory($category->name);

            foreach ($menuItems as $item) {
                MenuItem::create([
                    'name' => $item['name'],
                    'description' => $item['description'],
                    'price' => $item['price'],
                    'category_id' => $category->id,
                    'is_available' => true,
                    'sort_order' => $item['sort_order'],
                ]);
            }
        }
    }

    private function getMenuItemsByCategory($categoryName)
    {
        $menuItems = [
            'Bosh taomlar' => [
                ['name' => 'Osh', 'description' => 'An\'anaviy osh', 'price' => 25000, 'sort_order' => 1],
                ['name' => 'Lag\'mon', 'description' => 'Lag\'mon sho\'rva', 'price' => 30000, 'sort_order' => 2],
                ['name' => 'Manti', 'description' => 'Manti 10 dona', 'price' => 20000, 'sort_order' => 3],
            ],
            'Shorvalar' => [
                ['name' => 'Mastava', 'description' => 'Mastava sho\'rva', 'price' => 15000, 'sort_order' => 1],
                ['name' => 'Chuchvara', 'description' => 'Chuchvara sho\'rva', 'price' => 18000, 'sort_order' => 2],
            ],
            'Salatlar' => [
                ['name' => 'Achchiq-chuchuk', 'description' => 'Achchiq-chuchuk salat', 'price' => 12000, 'sort_order' => 1],
                ['name' => 'Olivye', 'description' => 'Olivye salat', 'price' => 15000, 'sort_order' => 2],
            ],
            'Ichimliklar' => [
                ['name' => 'Choy', 'description' => 'Qora choy', 'price' => 2000, 'sort_order' => 1],
                ['name' => 'Kofe', 'description' => 'Qora kofe', 'price' => 5000, 'sort_order' => 2],
                ['name' => 'Kola', 'description' => 'Coca-Cola', 'price' => 8000, 'sort_order' => 3],
            ],
            'Shirinliklar' => [
                ['name' => 'Halva', 'description' => 'Halva', 'price' => 10000, 'sort_order' => 1],
                ['name' => 'Baklava', 'description' => 'Baklava', 'price' => 12000, 'sort_order' => 2],
            ],
        ];

        return $menuItems[$categoryName] ?? [];
    }
} 