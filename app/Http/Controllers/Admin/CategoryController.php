<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Project;
use App\Models\Restaurant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class CategoryController extends Controller
{
    use AuthorizesRequests;

	public function __construct()
	{
		$this->middleware('check.plan.limits:categories')->only(['create', 'store']);
	}

    public function index(Restaurant $restaurant, Project $project)
    {
        $this->authorize('view', $restaurant);
        
        $categories = Category::where('project_id', $project->id)
            ->with(['menuItems'])
            ->orderBy('sort_order')
            ->get();
        
        return view('admin.categories.index', compact('restaurant', 'project', 'categories'));
    }

    public function create(Restaurant $restaurant, Project $project)
    {
        $this->authorize('update', $restaurant);
        
        return view('admin.categories.create', compact('restaurant', 'project'));
    }

    public function store(Request $request, Restaurant $restaurant, Project $project)
    {
        $this->authorize('update', $restaurant);
        
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean'
        ]);

        $category = Category::create([
            'project_id' => $project->id,
            'restaurant_id' => $restaurant->id,
            'name' => $request->name,
            'description' => $request->description,
            'sort_order' => $request->sort_order ?? 0,
            'is_active' => $request->has('is_active')
        ]);

        return redirect()->route('admin.categories.index', [$restaurant, $project])
            ->with('success', 'Kategoriya muvaffaqiyatli yaratildi.');
    }

    public function show(Restaurant $restaurant, Project $project, Category $category)
    {
        $this->authorize('view', $restaurant);
        
        $category->load(['menuItems']);
        
        return view('admin.categories.show', compact('restaurant', 'project', 'category'));
    }

    public function edit(Restaurant $restaurant, Project $project, Category $category)
    {
        $this->authorize('update', $restaurant);
        
        return view('admin.categories.edit', compact('restaurant', 'project', 'category'));
    }

    public function update(Request $request, Restaurant $restaurant, Project $project, Category $category)
    {
        $this->authorize('update', $restaurant);
        
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean'
        ]);

        $category->update([
            'name' => $request->name,
            'description' => $request->description,
            'sort_order' => $request->sort_order ?? 0,
            'is_active' => $request->has('is_active')
        ]);

        return redirect()->route('admin.categories.index', [$restaurant, $project])
            ->with('success', 'Kategoriya muvaffaqiyatli yangilandi.');
    }

    public function destroy(Restaurant $restaurant, Project $project, Category $category)
    {
        $this->authorize('update', $restaurant);
        
        $category->delete();

        return redirect()->route('admin.categories.index', [$restaurant, $project])
            ->with('success', 'Kategoriya muvaffaqiyatli o\'chirildi.');
    }
} 