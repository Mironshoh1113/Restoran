<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\Restaurant;
use Illuminate\Database\Seeder;

class ProjectSeeder extends Seeder
{
    public function run(): void
    {
        $restaurants = Restaurant::all();

        foreach ($restaurants as $restaurant) {
            Project::create([
                'name' => $restaurant->name . ' - Asosiy filial',
                'restaurant_id' => $restaurant->id,
                'description' => 'Asosiy filial',
                'is_active' => true,
            ]);

            Project::create([
                'name' => $restaurant->name . ' - 2-filial',
                'restaurant_id' => $restaurant->id,
                'description' => 'Ikkinchi filial',
                'is_active' => true,
            ]);
        }
    }
} 