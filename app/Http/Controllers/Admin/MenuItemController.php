<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MenuItem;
use App\Models\Category;
use App\Models\Project;
use App\Models\Restaurant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class MenuItemController extends Controller
{
    use AuthorizesRequests;

    public function index(Restaurant $restaurant, Project $project, Category $category)
    {
        $this->authorize('view', $restaurant);
        
        $menuItems = MenuItem::where('category_id', $category->id)
            ->orderBy('sort_order')
            ->get();
        
        return view('admin.menu-items.index', compact('restaurant', 'project', 'category', 'menuItems'));
    }

    public function create(Restaurant $restaurant, Project $project, Category $category)
    {
        $this->authorize('update', $restaurant);
        
        return view('admin.menu-items.create', compact('restaurant', 'project', 'category'));
    }

    public function store(Request $request, Restaurant $restaurant, Project $project, Category $category)
    {
        $this->authorize('update', $restaurant);
        
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean'
        ]);

        $data = [
            'category_id' => $category->id,
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'sort_order' => $request->sort_order ?? 0,
            'is_active' => $request->has('is_active')
        ];

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('menu-items', 'public');
            $data['image'] = $imagePath;
        }

        MenuItem::create($data);

        return redirect()->route('admin.menu-items.index', [$restaurant, $project, $category])
            ->with('success', 'Taom muvaffaqiyatli yaratildi.');
    }

    public function show(Restaurant $restaurant, Project $project, Category $category, MenuItem $menuItem)
    {
        $this->authorize('view', $restaurant);
        
        return view('admin.menu-items.show', compact('restaurant', 'project', 'category', 'menuItem'));
    }

    public function edit(Restaurant $restaurant, Project $project, Category $category, MenuItem $menuItem)
    {
        $this->authorize('update', $restaurant);
        
        return view('admin.menu-items.edit', compact('restaurant', 'project', 'category', 'menuItem'));
    }

    public function update(Request $request, Restaurant $restaurant, Project $project, Category $category, MenuItem $menuItem)
    {
        $this->authorize('update', $restaurant);
        
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean'
        ]);

        $data = [
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'sort_order' => $request->sort_order ?? 0,
            'is_active' => $request->has('is_active')
        ];

        if ($request->hasFile('image')) {
            // Delete old image
            if ($menuItem->image) {
                Storage::disk('public')->delete($menuItem->image);
            }
            
            $imagePath = $request->file('image')->store('menu-items', 'public');
            $data['image'] = $imagePath;
        }

        $menuItem->update($data);

        return redirect()->route('admin.menu-items.index', [$restaurant, $project, $category])
            ->with('success', 'Taom muvaffaqiyatli yangilandi.');
    }

    public function destroy(Restaurant $restaurant, Project $project, Category $category, MenuItem $menuItem)
    {
        $this->authorize('update', $restaurant);
        
        // Delete image
        if ($menuItem->image) {
            Storage::disk('public')->delete($menuItem->image);
        }
        
        $menuItem->delete();

        return redirect()->route('admin.menu-items.index', [$restaurant, $project, $category])
            ->with('success', 'Taom muvaffaqiyatli o\'chirildi.');
    }
} 