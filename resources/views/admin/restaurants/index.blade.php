@extends('admin.layouts.app')

@section('title', 'Restoranlar')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Restoranlar</h1>
            <p class="text-gray-600">Barcha restoranlarni boshqarish</p>
        </div>
        <button onclick="openCreateModal()" 
                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center space-x-2 transition-colors">
            <i class="fas fa-plus"></i>
            <span>Yangi restoran</span>
        </button>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Jami restoranlar</p>
                    <p class="text-3xl font-bold text-blue-600">{{ $restaurants->count() }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-store text-blue-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Faol restoranlar</p>
                    <p class="text-3xl font-bold text-green-600">{{ $restaurants->where('is_active', true)->count() }}</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-check-circle text-green-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Faol emas</p>
                    <p class="text-3xl font-bold text-red-600">{{ $restaurants->where('is_active', false)->count() }}</p>
                </div>
                <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-times-circle text-red-600 text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Restaurants Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($restaurants as $restaurant)
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 hover:shadow-md transition-all duration-200">
            <div class="p-6">
                <div class="flex items-start justify-between mb-4">
                    <div class="flex-1">
                        <h3 class="text-lg font-semibold text-gray-800 mb-1">{{ $restaurant->name }}</h3>
                        @if($restaurant->owner)
                            <p class="text-sm text-gray-500">Egasi: {{ $restaurant->owner->name }}</p>
                        @endif
                    </div>
                    <div class="flex items-center space-x-2">
                        @if($restaurant->is_active)
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                <i class="fas fa-check mr-1"></i>Faol
                            </span>
                        @else
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                <i class="fas fa-times mr-1"></i>Faol emas
                            </span>
                        @endif
                    </div>
                </div>

                <div class="space-y-3 mb-4">
                    <div class="flex items-center text-sm text-gray-600">
                        <i class="fas fa-phone w-4 mr-2"></i>
                        <span>{{ $restaurant->phone }}</span>
                    </div>
                    <div class="flex items-start text-sm text-gray-600">
                        <i class="fas fa-map-marker-alt w-4 mr-2 mt-0.5"></i>
                        <span class="line-clamp-2">{{ Str::limit($restaurant->address, 60) }}</span>
                    </div>
                    @if($restaurant->bot_username)
                        <div class="flex items-center text-sm text-gray-600">
                            <i class="fab fa-telegram w-4 mr-2"></i>
                            <span>@{{ $restaurant->bot_username }}</span>
                        </div>
                    @endif
                </div>

                <div class="flex items-center justify-between pt-4 border-t border-gray-100">
                    <div class="flex items-center space-x-2">
                        <a href="{{ route('admin.restaurants.show', $restaurant) }}" 
                           class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors">
                            <i class="fas fa-eye"></i>
                        </a>
                        <button onclick="editRestaurant({{ $restaurant->id }})" 
                                class="p-2 text-yellow-600 hover:bg-yellow-50 rounded-lg transition-colors">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button onclick="deleteRestaurant({{ $restaurant->id }})" 
                                class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                    <span class="text-xs text-gray-500">{{ $restaurant->created_at->format('d.m.Y') }}</span>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    @if($restaurants->count() === 0)
        <div class="text-center py-12">
            <i class="fas fa-store text-gray-400 text-6xl mb-4"></i>
            <h3 class="text-lg font-medium text-gray-900 mb-2">Hali restoranlar yo'q</h3>
            <p class="text-gray-500 mb-6">Birinchi restoranni qo'shish uchun tugmani bosing</p>
            <button onclick="openCreateModal()" 
                    class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg flex items-center space-x-2 mx-auto">
                <i class="fas fa-plus"></i>
                <span>Restoran qo'shish</span>
            </button>
        </div>
    @endif
</div>

<!-- Create/Edit Modal -->
<div id="restaurantModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-xl shadow-lg max-w-md w-full mx-4 max-h-[90vh] overflow-y-auto">
        <div class="p-6 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h3 id="modalTitle" class="text-lg font-semibold text-gray-800">Yangi restoran</h3>
                <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
        </div>
        
        <form id="restaurantForm" class="p-6 space-y-4">
            @csrf
            <div>
                <label for="restaurantName" class="block text-sm font-medium text-gray-700 mb-2">Restoran nomi</label>
                <input type="text" id="restaurantName" name="name" required
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>
            
            <div>
                <label for="restaurantPhone" class="block text-sm font-medium text-gray-700 mb-2">Telefon raqam</label>
                <input type="text" id="restaurantPhone" name="phone" required
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>
            
            <div>
                <label for="restaurantAddress" class="block text-sm font-medium text-gray-700 mb-2">Manzil</label>
                <textarea id="restaurantAddress" name="address" rows="3" required
                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"></textarea>
            </div>
            
            <div>
                <label for="restaurantBotUsername" class="block text-sm font-medium text-gray-700 mb-2">Bot username</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500">@</span>
                    <input type="text" id="restaurantBotUsername" name="bot_username"
                           class="w-full pl-8 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           placeholder="bot_username">
                </div>
            </div>
            
            <div>
                <label for="restaurantBotToken" class="block text-sm font-medium text-gray-700 mb-2">Bot token</label>
                <input type="text" id="restaurantBotToken" name="bot_token"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                       placeholder="1234567890:ABCdefGHIjklMNOpqrsTUVwxyz">
            </div>
            
            <div>
                <label class="flex items-center">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" id="restaurantActive" name="is_active" value="1" checked
                           class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                    <span class="ml-2 text-sm text-gray-700">Faol</span>
                </label>
            </div>
        </form>
        
        <div class="p-6 border-t border-gray-200 flex items-center justify-end space-x-3">
            <button onclick="closeModal()" 
                    class="px-4 py-2 text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">
                Bekor qilish
            </button>
            <button onclick="saveRestaurant()" 
                    class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">
                Saqlash
            </button>
        </div>
    </div>
</div>

<script>
let currentRestaurantId = null;

function openCreateModal() {
    currentRestaurantId = null;
    document.getElementById('modalTitle').textContent = 'Yangi restoran';
    document.getElementById('restaurantForm').reset();
    document.getElementById('restaurantActive').checked = true;
    document.getElementById('restaurantModal').classList.remove('hidden');
}

function editRestaurant(id) {
    currentRestaurantId = id;
    document.getElementById('modalTitle').textContent = 'Restoranni tahrirlash';
    
    // Show loading state
    const modal = document.getElementById('restaurantModal');
    modal.classList.remove('hidden');
    
    // Show loading indicator
    const form = document.getElementById('restaurantForm');
    form.style.opacity = '0.5';
    form.style.pointerEvents = 'none';
    
    // Load restaurant data via AJAX
    fetch(`/admin/restaurants/${id}/edit`, {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }
        return response.json();
    })
    .then(data => {
        // Populate form fields
        document.getElementById('restaurantName').value = data.name || '';
        document.getElementById('restaurantPhone').value = data.phone || '';
        document.getElementById('restaurantAddress').value = data.address || '';
        document.getElementById('restaurantBotToken').value = data.bot_token || '';
        document.getElementById('restaurantBotUsername').value = data.bot_username || '';
        document.getElementById('restaurantActive').checked = data.is_active || false;
        
        // Restore form interactivity
        form.style.opacity = '1';
        form.style.pointerEvents = 'auto';
    })
    .catch(error => {
        console.error('Error loading restaurant data:', error);
        
        // Show error message
        alert('Ma\'lumotlarni yuklashda xatolik yuz berdi. Iltimos, qaytadan urinib ko\'ring.');
        
        // Restore form interactivity
        form.style.opacity = '1';
        form.style.pointerEvents = 'auto';
        
        // Close modal on error
        closeModal();
    });
}

function closeModal() {
    document.getElementById('restaurantModal').classList.add('hidden');
    currentRestaurantId = null;
    
    // Reset form state
    const form = document.getElementById('restaurantForm');
    form.style.opacity = '1';
    form.style.pointerEvents = 'auto';
}

function saveRestaurant() {
    const form = document.getElementById('restaurantForm');
    const formData = new FormData(form);
    
    const url = currentRestaurantId 
        ? `/admin/restaurants/${currentRestaurantId}` 
        : '/admin/restaurants';
    
    const method = currentRestaurantId ? 'PUT' : 'POST';
    
    // Add CSRF token
    formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
    if (currentRestaurantId) {
        formData.append('_method', 'PUT');
    }
    
    // Handle is_active checkbox properly
    const isActive = document.getElementById('restaurantActive').checked;
    formData.set('is_active', isActive ? '1' : '0');
    
    // Debug: Log form data
    console.log('Form data being sent:');
    for (let [key, value] of formData.entries()) {
        console.log(key + ': ' + value);
    }
    
    // Show loading state
    const submitBtn = document.querySelector('button[onclick="saveRestaurant()"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saqlanmoqda...';
    submitBtn.disabled = true;
    
    fetch(url, {
        method: method,
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => {
        console.log('Response status:', response.status);
        console.log('Response headers:', response.headers);
        
        if (response.redirected) {
            window.location.href = response.url;
            return;
        }
        
        if (!response.ok) {
            return response.text().then(text => {
                console.log('Error response text:', text);
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            });
        }
        
        return response.json();
    })
    .then(data => {
        console.log('Response data:', data);
        
        if (data && data.success) {
            closeModal();
            location.reload();
        } else if (data && data.message) {
            alert(data.message);
            closeModal();
            location.reload();
        } else {
            // Success - page will reload
            closeModal();
            location.reload();
        }
    })
    .catch(error => {
        console.error('Error saving restaurant:', error);
        
        let errorMessage = 'Xatolik yuz berdi: ';
        if (error.message.includes('404')) {
            errorMessage += 'Restoran topilmadi';
        } else if (error.message.includes('500')) {
            errorMessage += 'Server xatosi. Iltimos, keyinroq urinib ko\'ring';
        } else {
            errorMessage += error.message;
        }
        
        alert(errorMessage);
    })
    .finally(() => {
        // Restore button state
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    });
}

function deleteRestaurant(id) {
    if (confirm('Haqiqatan ham bu restoranni o\'chirmoqchimisiz? Bu amalni qaytarib bo\'lmaydi.')) {
        fetch(`/admin/restaurants/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            }
        })
        .then(response => {
            if (response.redirected) {
                window.location.href = response.url;
                return;
            }
            
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }
            
            return response.json();
        })
        .then(data => {
            if (data && data.success) {
                location.reload();
            } else {
                alert('Restoranni o\'chirishda xatolik yuz berdi');
            }
        })
        .catch(error => {
            console.error('Error deleting restaurant:', error);
            alert('Xatolik yuz berdi: ' + error.message);
        });
    }
}

// Close modal when clicking outside
document.getElementById('restaurantModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeModal();
    }
});

// Close modal with Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeModal();
    }
});
</script>
@endsection 