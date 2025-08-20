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

	public function __construct()
	{
		$this->middleware('check.plan.limits:menu_items')->only(['create', 'store']);
	}

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
		
		// Debug: Log request information
		\Log::info('Menu item store request', [
			'has_file' => $request->hasFile('image'),
			'all_files' => $request->allFiles(),
			'request_data' => $request->all()
		]);
		
		$request->validate([
			'name' => 'required|string|max:255',
			'description' => 'nullable|string',
			'price' => 'required|numeric|min:0',
			'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
			'sort_order' => 'nullable|integer|min:0',
			'is_active' => 'boolean'
		]);

		$data = [
			'category_id' => $category->id,
			'restaurant_id' => $restaurant->id,
			'name' => $request->name,
			'description' => $request->description,
			'price' => $request->price,
			'sort_order' => $request->sort_order ?? 0,
			'is_active' => $request->has('is_active')
		];

		if ($request->hasFile('image')) {
			try {
				// Use Laravel validation
				$request->validate([
					'image' => 'image|mimes:jpeg,png,jpg,gif|max:5120' // 5MB = 5120 KB
				]);
				
				// Store using Laravel Storage (consistent with Restaurant images)
				$imagePath = $request->file('image')->store('menu-items', 'public');
				$data['image'] = $imagePath;
				
				\Log::info('Menu item image uploaded successfully', [
					'stored_path' => $imagePath,
					'full_url' => asset('storage/' . $imagePath)
				]);
				
			} catch (\Exception $e) {
				\Log::error('Menu item image upload failed', [
					'error' => $e->getMessage()
				]);
				
				return redirect()->back()
					->withInput()
					->withErrors(['image' => $e->getMessage()]);
			}
		}

		$menuItem = MenuItem::create($data);
		
		\Log::info('Menu item created successfully', [
			'menu_item_id' => $menuItem->id,
			'image_path' => $menuItem->image ?? 'none'
		]);

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
		
		// Debug: Log request information
		\Log::info('Menu item update request', [
			'has_file' => $request->hasFile('image'),
			'all_files' => $request->allFiles(),
			'request_data' => $request->all(),
			'menu_item_id' => $menuItem->id
		]);
		
		$request->validate([
			'name' => 'required|string|max:255',
			'description' => 'nullable|string',
			'price' => 'required|numeric|min:0',
			'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
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
			try {
				// Use Laravel validation
				$request->validate([
					'image' => 'image|mimes:jpeg,png,jpg,gif|max:5120' // 5MB = 5120 KB
				]);
				
				// Delete old image if exists
				if ($menuItem->image) {
					\Storage::disk('public')->delete($menuItem->image);
				}
				
				// Store using Laravel Storage (consistent with Restaurant images)
				$imagePath = $request->file('image')->store('menu-items', 'public');
				$data['image'] = $imagePath;
				
				\Log::info('Menu item image updated successfully', [
					'old_path' => $menuItem->image,
					'new_path' => $imagePath,
					'full_url' => asset('storage/' . $imagePath)
				]);
				
			} catch (\Exception $e) {
				\Log::error('Menu item image update failed', [
					'error' => $e->getMessage()
				]);
				
				return redirect()->back()
					->withInput()
					->withErrors(['image' => 'Rasm yangilashda xatolik yuz berdi: ' . $e->getMessage()]);
			}
		}

		$menuItem->update($data);
		
		\Log::info('Menu item updated successfully', [
			'menu_item_id' => $menuItem->id,
			'image_path' => $menuItem->image ?? 'none'
		]);

		return redirect()->route('admin.menu-items.index', [$restaurant, $project, $category])
			->with('success', 'Taom muvaffaqiyatli yangilandi.');
	}

	public function destroy(Restaurant $restaurant, Project $project, Category $category, MenuItem $menuItem)
	{
		$this->authorize('update', $restaurant);
		
		// Delete image
		if ($menuItem->image) {
			$oldPath = public_path($menuItem->image);
			if (file_exists($oldPath)) {
				if (unlink($oldPath)) {
					\Log::info('Old menu item image deleted', ['old_path' => $menuItem->image]);
				} else {
					\Log::warning('Failed to delete old image', ['old_path' => $menuItem->image]);
				}
			}
		}
		
		$menuItem->delete();

		return redirect()->route('admin.menu-items.index', [$restaurant, $project, $category])
			->with('success', 'Taom muvaffaqiyatli o\'chirildi.');
	}
} 