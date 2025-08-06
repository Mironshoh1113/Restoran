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
            'restaurant_id' => $restaurant->id,
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'sort_order' => $request->sort_order ?? 0,
            'is_active' => $request->has('is_active')
        ];

        if ($request->hasFile('image')) {
            try {
                $file = $request->file('image');
                
                // Validate file
                if (!$file->isValid()) {
                    throw new \Exception('Fayl yuklashda xatolik yuz berdi');
                }
                
                // Check file size (max 2MB)
                if ($file->getSize() > 2 * 1024 * 1024) {
                    throw new \Exception('Fayl hajmi 2MB dan katta bo\'lishi mumkin emas');
                }
                
                // Check file type
                $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/jpg'];
                if (!in_array($file->getMimeType(), $allowedTypes)) {
                    throw new \Exception('Faqat rasm fayllari qabul qilinadi (JPEG, PNG, GIF)');
                }
                
                $imagePath = $file->store('menu-items', 'public');
                $data['image'] = $imagePath;
                
                // Verify file was saved
                if (!Storage::disk('public')->exists($imagePath)) {
                    throw new \Exception('Rasm saqlashda xatolik yuz berdi');
                }
                
                // Log successful upload
                \Log::info('Menu item image uploaded successfully', [
                    'original_name' => $file->getClientOriginalName(),
                    'image_path' => $imagePath,
                    'full_path' => storage_path('app/public/' . $imagePath),
                    'file_size' => $file->getSize(),
                    'mime_type' => $file->getMimeType(),
                    'exists' => Storage::disk('public')->exists($imagePath)
                ]);
            } catch (\Exception $e) {
                \Log::error('Failed to upload menu item image', [
                    'error' => $e->getMessage(),
                    'file' => $request->file('image')->getClientOriginalName(),
                    'file_size' => $request->file('image')->getSize(),
                    'mime_type' => $request->file('image')->getMimeType()
                ]);
                
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['image' => 'Rasm yuklashda xatolik yuz berdi: ' . $e->getMessage()]);
            }
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
            try {
                $file = $request->file('image');
                
                // Validate file
                if (!$file->isValid()) {
                    throw new \Exception('Fayl yuklashda xatolik yuz berdi');
                }
                
                // Check file size (max 2MB)
                if ($file->getSize() > 2 * 1024 * 1024) {
                    throw new \Exception('Fayl hajmi 2MB dan katta bo\'lishi mumkin emas');
                }
                
                // Check file type
                $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/jpg'];
                if (!in_array($file->getMimeType(), $allowedTypes)) {
                    throw new \Exception('Faqat rasm fayllari qabul qilinadi (JPEG, PNG, GIF)');
                }
                
                // Delete old image
                if ($menuItem->image && Storage::disk('public')->exists($menuItem->image)) {
                    Storage::disk('public')->delete($menuItem->image);
                    \Log::info('Old menu item image deleted', ['old_path' => $menuItem->image]);
                }
                
                $imagePath = $file->store('menu-items', 'public');
                $data['image'] = $imagePath;
                
                // Verify file was saved
                if (!Storage::disk('public')->exists($imagePath)) {
                    throw new \Exception('Rasm saqlashda xatolik yuz berdi');
                }
                
                // Log successful upload
                \Log::info('Menu item image updated successfully', [
                    'original_name' => $file->getClientOriginalName(),
                    'image_path' => $imagePath,
                    'full_path' => storage_path('app/public/' . $imagePath),
                    'file_size' => $file->getSize(),
                    'mime_type' => $file->getMimeType(),
                    'exists' => Storage::disk('public')->exists($imagePath)
                ]);
            } catch (\Exception $e) {
                \Log::error('Failed to update menu item image', [
                    'error' => $e->getMessage(),
                    'file' => $request->file('image')->getClientOriginalName(),
                    'file_size' => $request->file('image')->getSize(),
                    'mime_type' => $request->file('image')->getMimeType()
                ]);
                
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['image' => 'Rasm yangilashda xatolik yuz berdi: ' . $e->getMessage()]);
            }
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