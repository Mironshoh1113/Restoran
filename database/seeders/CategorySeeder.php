<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Project;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $projects = Project::all();

        foreach ($projects as $project) {
            $categories = [
                ['name' => 'Bosh taomlar', 'sort_order' => 1],
                ['name' => 'Shorvalar', 'sort_order' => 2],
                ['name' => 'Salatlar', 'sort_order' => 3],
                ['name' => 'Ichimliklar', 'sort_order' => 4],
                ['name' => 'Shirinliklar', 'sort_order' => 5],
            ];

            foreach ($categories as $category) {
                Category::create([
                    'name' => $category['name'],
                    'project_id' => $project->id,
                    'description' => $category['name'] . ' kategoriyasi',
                    'sort_order' => $category['sort_order'],
                    'is_active' => true,
                ]);
            }
        }
    }
} 