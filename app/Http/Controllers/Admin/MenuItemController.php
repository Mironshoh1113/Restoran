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
                
                // Debug: Log file information
                \Log::info('File upload attempt', [
                    'original_name' => $file->getClientOriginalName(),
                    'file_size' => $file->getSize(),
                    'mime_type' => $file->getMimeType(),
                    'is_valid' => $file->isValid(),
                    'error' => $file->getError(),
                    'error_message' => $file->getErrorMessage(),
                    'real_path' => $file->getRealPath(),
                    'temporary_path' => $file->getPathname()
                ]);
                
                // Validate file
                if (!$file->isValid()) {
                    throw new \Exception('Fayl yuklashda xatolik yuz berdi: ' . $file->getErrorMessage());
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
                
                // Ensure storage directory exists with proper permissions
                $storagePath = public_path('img/menu');
                \Log::info('Storage path check', [
                    'storage_path' => $storagePath,
                    'exists' => is_dir($storagePath),
                    'writable' => is_writable($storagePath),
                    'permissions' => is_dir($storagePath) ? substr(sprintf('%o', fileperms($storagePath)), -4) : 'N/A'
                ]);
                
                if (!is_dir($storagePath)) {
                    if (!mkdir($storagePath, 0755, true)) {
                        throw new \Exception('Storage papkasini yaratishda xatolik yuz berdi');
                    }
                    \Log::info('Created storage directory', ['path' => $storagePath]);
                }
                
                // Check if directory is writable
                if (!is_writable($storagePath)) {
                    throw new \Exception('Storage papkasi yozish uchun ochiq emas');
                }
                
                // Test file creation
                $testFile = $storagePath . '/test_' . time() . '.txt';
                $testContent = 'Test file created at ' . date('Y-m-d H:i:s');
                if (file_put_contents($testFile, $testContent) === false) {
                    throw new \Exception('Storage papkasiga yozishda xatolik yuz berdi');
                } else {
                    \Log::info('Test file created successfully', ['test_file' => $testFile]);
                    // Clean up test file
                    unlink($testFile);
                }
                
                // Generate unique filename with timestamp
                $extension = strtolower($file->getClientOriginalExtension());
                $filename = uniqid() . '_' . time() . '.' . $extension;
                $imagePath = 'img/menu/' . $filename;
                $fullPath = public_path($imagePath);
                
                // Multiple save attempts with different methods
                $saved = false;
                $error = '';
                
                // Method 1: Using direct file copy
                try {
                    $saved = copy($file->getRealPath(), $fullPath);
                    if ($saved) {
                        \Log::info('File saved using direct copy', ['path' => $fullPath]);
                    }
                } catch (\Exception $e) {
                    $error .= 'Direct copy error: ' . $e->getMessage() . '; ';
                }
                
                // Method 2: Using move_uploaded_file if copy failed
                if (!$saved) {
                    try {
                        $saved = move_uploaded_file($file->getRealPath(), $fullPath);
                        if ($saved) {
                            \Log::info('File saved using move_uploaded_file', ['path' => $fullPath]);
                        }
                    } catch (\Exception $e) {
                        $error .= 'Move uploaded file error: ' . $e->getMessage() . '; ';
                    }
                }
                
                if (!$saved) {
                    throw new \Exception('Fayl saqlashda xatolik yuz berdi. Xatoliklar: ' . $error);
                }
                
                $data['image'] = $imagePath;
                
                // Verify file was saved
                if (!file_exists($fullPath)) {
                    throw new \Exception('Rasm saqlashda xatolik yuz berdi - fayl topilmadi');
                }
                
                // Check file size after save
                $savedFileSize = filesize($fullPath);
                if ($savedFileSize === 0) {
                    throw new \Exception('Saqlangan fayl bo\'sh');
                }
                
                // Log successful upload
                \Log::info('Menu item image uploaded successfully', [
                    'original_name' => $file->getClientOriginalName(),
                    'image_path' => $imagePath,
                    'full_path' => $fullPath,
                    'file_size' => $file->getSize(),
                    'saved_file_size' => $savedFileSize,
                    'mime_type' => $file->getMimeType(),
                    'exists' => file_exists($fullPath),
                    'url' => asset($imagePath),
                    'saved' => $saved,
                    'content_length' => strlen($fileContent ?? ''),
                    'server_info' => [
                        'document_root' => $_SERVER['DOCUMENT_ROOT'] ?? 'N/A',
                        'script_name' => $_SERVER['SCRIPT_NAME'] ?? 'N/A',
                        'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'N/A',
                        'storage_path' => storage_path(),
                        'public_path' => public_path(),
                        'base_path' => base_path()
                    ]
                ]);
            } catch (\Exception $e) {
                \Log::error('Failed to upload menu item image', [
                    'error' => $e->getMessage(),
                    'file' => $request->file('image')->getClientOriginalName(),
                    'file_size' => $request->file('image')->getSize(),
                    'mime_type' => $request->file('image')->getMimeType(),
                    'is_valid' => $request->file('image')->isValid(),
                    'error_code' => $request->file('image')->getError()
                ]);
                
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['image' => 'Rasm yuklashda xatolik yuz berdi: ' . $e->getMessage()]);
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
                
                // Debug: Log file information
                \Log::info('File update attempt', [
                    'original_name' => $file->getClientOriginalName(),
                    'file_size' => $file->getSize(),
                    'mime_type' => $file->getMimeType(),
                    'is_valid' => $file->isValid(),
                    'error' => $file->getError(),
                    'error_message' => $file->getErrorMessage(),
                    'real_path' => $file->getRealPath(),
                    'temporary_path' => $file->getPathname()
                ]);
                
                // Validate file
                if (!$file->isValid()) {
                    throw new \Exception('Fayl yuklashda xatolik yuz berdi: ' . $file->getErrorMessage());
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
                
                // Ensure storage directory exists with proper permissions
                $storagePath = public_path('img/menu');
                \Log::info('Storage path check (update)', [
                    'storage_path' => $storagePath,
                    'exists' => is_dir($storagePath),
                    'writable' => is_writable($storagePath),
                    'permissions' => is_dir($storagePath) ? substr(sprintf('%o', fileperms($storagePath)), -4) : 'N/A'
                ]);
                
                if (!is_dir($storagePath)) {
                    if (!mkdir($storagePath, 0755, true)) {
                        throw new \Exception('Storage papkasini yaratishda xatolik yuz berdi');
                    }
                    \Log::info('Created storage directory', ['path' => $storagePath]);
                }
                
                // Check if directory is writable
                if (!is_writable($storagePath)) {
                    throw new \Exception('Storage papkasi yozish uchun ochiq emas');
                }
                
                // Test file creation
                $testFile = $storagePath . '/test_' . time() . '.txt';
                $testContent = 'Test file created at ' . date('Y-m-d H:i:s');
                if (file_put_contents($testFile, $testContent) === false) {
                    throw new \Exception('Storage papkasiga yozishda xatolik yuz berdi');
                } else {
                    \Log::info('Test file created successfully (update)', ['test_file' => $testFile]);
                    // Clean up test file
                    unlink($testFile);
                }
                
                // Delete old image
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
                
                // Generate unique filename with timestamp
                $extension = strtolower($file->getClientOriginalExtension());
                $filename = uniqid() . '_' . time() . '.' . $extension;
                $imagePath = 'img/menu/' . $filename;
                $fullPath = public_path($imagePath);
                
                // Multiple save attempts with different methods
                $saved = false;
                $error = '';
                
                // Method 1: Using direct file copy
                try {
                    $saved = copy($file->getRealPath(), $fullPath);
                    if ($saved) {
                        \Log::info('File saved using direct copy', ['path' => $fullPath]);
                    }
                } catch (\Exception $e) {
                    $error .= 'Direct copy error: ' . $e->getMessage() . '; ';
                }
                
                // Method 2: Using move_uploaded_file if copy failed
                if (!$saved) {
                    try {
                        $saved = move_uploaded_file($file->getRealPath(), $fullPath);
                        if ($saved) {
                            \Log::info('File saved using move_uploaded_file', ['path' => $fullPath]);
                        }
                    } catch (\Exception $e) {
                        $error .= 'Move uploaded file error: ' . $e->getMessage() . '; ';
                    }
                }
                
                if (!$saved) {
                    throw new \Exception('Fayl saqlashda xatolik yuz berdi. Xatoliklar: ' . $error);
                }
                
                $data['image'] = $imagePath;
                
                // Verify file was saved
                if (!file_exists($fullPath)) {
                    throw new \Exception('Rasm saqlashda xatolik yuz berdi - fayl topilmadi');
                }
                
                // Check file size after save
                $savedFileSize = filesize($fullPath);
                if ($savedFileSize === 0) {
                    throw new \Exception('Saqlangan fayl bo\'sh');
                }
                
                // Log successful upload
                \Log::info('Menu item image updated successfully', [
                    'original_name' => $file->getClientOriginalName(),
                    'image_path' => $imagePath,
                    'full_path' => $fullPath,
                    'file_size' => $file->getSize(),
                    'saved_file_size' => $savedFileSize,
                    'mime_type' => $file->getMimeType(),
                    'exists' => file_exists($fullPath),
                    'url' => asset($imagePath),
                    'saved' => $saved,
                    'content_length' => strlen($fileContent ?? ''),
                    'server_info' => [
                        'document_root' => $_SERVER['DOCUMENT_ROOT'] ?? 'N/A',
                        'script_name' => $_SERVER['SCRIPT_NAME'] ?? 'N/A',
                        'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'N/A',
                        'storage_path' => storage_path(),
                        'public_path' => public_path(),
                        'base_path' => base_path()
                    ]
                ]);
            } catch (\Exception $e) {
                \Log::error('Failed to update menu item image', [
                    'error' => $e->getMessage(),
                    'file' => $request->file('image')->getClientOriginalName(),
                    'file_size' => $request->file('image')->getSize(),
                    'mime_type' => $request->file('image')->getMimeType(),
                    'is_valid' => $request->file('image')->isValid(),
                    'error_code' => $request->file('image')->getError()
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