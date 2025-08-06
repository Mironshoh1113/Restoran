@extends('admin.layouts.app')

@section('title', 'Taomni tahrirlash')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">{{ $restaurant->name }} - {{ $project->name }}</h1>
            <p class="text-gray-600">{{ $category->name }} - {{ $menuItem->name }} ni tahrirlash</p>
        </div>
        <a href="{{ route('admin.menu-items.index', [$restaurant, $project, $category]) }}" 
           class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg flex items-center space-x-2 transition-colors">
            <i class="fas fa-arrow-left"></i>
            <span>Orqaga</span>
        </a>
    </div>

    <!-- Form -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="p-6 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-800">Taom ma'lumotlari</h2>
        </div>
        
        <form action="{{ route('admin.menu-items.update', [$restaurant, $project, $category, $menuItem]) }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-6" id="menu-item-form">
            @csrf
            @method('PATCH')
            
            <!-- Debug info -->
            @if($errors->any())
                <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                    <h3 class="text-red-800 font-medium">Xatoliklar:</h3>
                    <ul class="mt-2 text-sm text-red-700">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                        Taom nomi <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           id="name" 
                           name="name" 
                           value="{{ old('name', $menuItem->name) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           placeholder="Masalan: Plov">
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="price" class="block text-sm font-medium text-gray-700 mb-2">
                        Narx (so'm) <span class="text-red-500">*</span>
                    </label>
                    <input type="number" 
                           id="price" 
                           name="price" 
                           value="{{ old('price', $menuItem->price) }}"
                           min="0"
                           step="100"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           placeholder="25000">
                    @error('price')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="sort_order" class="block text-sm font-medium text-gray-700 mb-2">
                        Tartib raqami
                    </label>
                    <input type="number" 
                           id="sort_order" 
                           name="sort_order" 
                           value="{{ old('sort_order', $menuItem->sort_order) }}"
                           min="0"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           placeholder="0">
                    @error('sort_order')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="image" class="block text-sm font-medium text-gray-700 mb-2">
                        Rasm
                    </label>
                    <div class="space-y-2">
                        @if($menuItem->hasImage())
                            <div class="mb-2">
                                <img src="{{ $menuItem->image_url }}" 
                                     alt="{{ $menuItem->name }}"
                                     class="w-32 h-32 object-cover rounded-lg border border-gray-300"
                                     id="current-image"
                                     onerror="console.log('Edit form image failed to load:', this.src); this.style.display='none';">
                                <p class="text-xs text-gray-500 mt-1">Joriy rasm</p>
                            </div>
                        @endif
                        <div id="image-preview" class="hidden">
                            <img src="" alt="Preview" class="w-32 h-32 object-cover rounded-lg border border-gray-300">
                            <p class="text-xs text-gray-500 mt-1">Yangi rasm</p>
                        </div>
                        <input type="file" 
                               id="image" 
                               name="image" 
                               accept="image/*"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <p class="text-xs text-gray-500">Rasm yuklash uchun fayl tanlang (JPEG, PNG, GIF, maksimal 2MB)</p>
                        <div id="file-info" class="text-xs text-gray-600 hidden">
                            <p>Fayl nomi: <span id="file-name"></span></p>
                            <p>Fayl hajmi: <span id="file-size"></span></p>
                            <p>Fayl turi: <span id="file-type"></span></p>
                        </div>
                    </div>
                    @error('image')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div>
                <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                    Izoh
                </label>
                <textarea id="description" 
                          name="description" 
                          rows="4"
                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                          placeholder="Taom haqida qisqacha ma'lumot...">{{ old('description', $menuItem->description) }}</textarea>
                @error('description')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center">
                <input type="checkbox" 
                       id="is_active" 
                       name="is_active" 
                       value="1"
                       {{ old('is_active', $menuItem->is_active) ? 'checked' : '' }}
                       class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                <label for="is_active" class="ml-2 text-sm text-gray-700">
                    Taom faol
                </label>
            </div>

            <div class="flex items-center justify-end space-x-3 pt-6 border-t border-gray-200">
                <a href="{{ route('admin.menu-items.index', [$restaurant, $project, $category]) }}" 
                   class="px-4 py-2 text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">
                    Bekor qilish
                </a>
                <button type="submit" 
                        class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">
                    <i class="fas fa-save mr-2"></i>
                    Saqlash
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const imageInput = document.getElementById('image');
    const imagePreview = document.getElementById('image-preview');
    const previewImg = imagePreview.querySelector('img');
    const currentImage = document.getElementById('current-image');
    const fileInfoDiv = document.getElementById('file-info');
    const fileNameSpan = document.getElementById('file-name');
    const fileSizeSpan = document.getElementById('file-size');
    const fileTypeSpan = document.getElementById('file-type');
    const form = document.getElementById('menu-item-form');
    
    imageInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        
        if (file) {
            // Show preview
            const reader = new FileReader();
            reader.onload = function(e) {
                previewImg.src = e.target.result;
                imagePreview.classList.remove('hidden');
                
                // Hide current image if exists
                if (currentImage) {
                    currentImage.parentElement.style.display = 'none';
                }
            };
            reader.readAsDataURL(file);
            
            // Display file info
            fileNameSpan.textContent = file.name;
            fileSizeSpan.textContent = (file.size / 1024).toFixed(2) + ' KB';
            fileTypeSpan.textContent = file.type;
            fileInfoDiv.classList.remove('hidden');
            
            console.log('Image selected:', file.name, 'Size:', file.size, 'bytes', 'Type:', file.type);
        } else {
            // Hide preview and file info
            imagePreview.classList.add('hidden');
            fileInfoDiv.classList.add('hidden');
            
            // Show current image if exists
            if (currentImage) {
                currentImage.parentElement.style.display = 'block';
            }
        }
    });
    
    // Form submission handling
    form.addEventListener('submit', function(e) {
        const submitBtn = form.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        
        // Show loading state
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Saqlanmoqda...';
        submitBtn.disabled = true;
        
        // Log form data
        const formData = new FormData(form);
        console.log('Form submission started');
        console.log('Image file:', formData.get('image'));
        
        // Allow form to submit normally
        // The loading state will be handled by the page reload
    });
    
    // Debug: Log current image info
    if (currentImage) {
        console.log('Current image src:', currentImage.src);
        console.log('Current image exists:', currentImage.complete && currentImage.naturalHeight !== 0);
        
        // Test image loading
        currentImage.addEventListener('load', function() {
            console.log('Current image loaded successfully');
        });
        
        currentImage.addEventListener('error', function() {
            console.log('Current image failed to load');
        });
    }
});
</script>
@endsection 