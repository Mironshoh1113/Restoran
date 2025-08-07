@extends('admin.layouts.app')

@section('title', 'Barcha foydalanuvchilar')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Barcha foydalanuvchilar</h1>
            <p class="text-gray-600">Barcha botlardagi foydalanuvchilarni boshqarish</p>
        </div>
        <div class="flex items-center space-x-3">
            <button onclick="showBulkMessageModal()" 
                    class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors">
                <i class="fas fa-paper-plane mr-2"></i>Xabar yuborish
            </button>
            <a href="{{ route('admin.bots.index') }}" 
               class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                <i class="fas fa-arrow-left mr-2"></i>Orqaga
            </a>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Restoran</label>
                <select id="restaurantFilter" onchange="filterUsers()" 
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Barcha restoranlar</option>
                    @foreach($restaurants as $restaurant)
                        <option value="{{ $restaurant->id }}">{{ $restaurant->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Qidirish</label>
                <input type="text" id="searchFilter" placeholder="Ism, familiya yoki username..." 
                       onkeyup="filterUsers()"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Holat</label>
                <select id="statusFilter" onchange="filterUsers()" 
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Barcha</option>
                    <option value="active">Faol</option>
                    <option value="inactive">Faol emas</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Users Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="p-6 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-800">Foydalanuvchilar ro'yxati</h3>
                <div class="flex items-center space-x-2">
                    <span class="text-sm text-gray-500" id="userCount">{{ $users->total() }} ta foydalanuvchi</span>
                    <button onclick="selectAllUsers()" class="text-blue-600 hover:text-blue-700 text-sm">
                        Hammasini tanlash
                    </button>
                </div>
            </div>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <input type="checkbox" id="selectAll" onchange="toggleSelectAll()">
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Foydalanuvchi
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Restoran
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Holat
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Oxirgi faollik
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Amallar
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($users as $user)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <input type="checkbox" class="user-checkbox" value="{{ $user->telegram_id }}" 
                                   data-restaurant="{{ $user->restaurant_id }}">
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center">
                                    <span class="text-white font-semibold text-sm">
                                        {{ strtoupper(substr($user->first_name ?? 'U', 0, 1)) }}
                                    </span>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ $user->full_name }}
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        @if($user->username)
                                            @{{ $user->username }}
                                        @else
                                            ID: {{ $user->telegram_id }}
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $user->restaurant->name }}</div>
                            <div class="text-sm text-gray-500">@{{ $user->restaurant->bot_username }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($user->is_active)
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                    <i class="fas fa-check mr-1"></i>Faol
                                </span>
                            @else
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                    <i class="fas fa-times mr-1"></i>Faol emas
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $user->last_activity ? $user->last_activity->diffForHumans() : 'Noma\'lum' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex items-center space-x-2">
                                <a href="{{ route('admin.bots.conversation', ['restaurant' => $user->restaurant_id, 'telegramUser' => $user->id]) }}" 
                                   class="text-blue-600 hover:text-blue-700" title="Suhbat">
                                    <i class="fas fa-comments"></i>
                                </a>
                                <button onclick="sendMessageToUser({{ $user->telegram_id }}, '{{ $user->restaurant_id }}')" 
                                        class="text-purple-600 hover:text-purple-700" title="Xabar yuborish">
                                    <i class="fas fa-paper-plane"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        @if($users->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $users->links() }}
        </div>
        @endif
    </div>
</div>

<!-- Bulk Message Modal -->
<div id="bulkMessageModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-xl shadow-xl max-w-lg w-full">
            <div class="p-6 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-800">Xabar yuborish</h3>
                    <button onclick="hideBulkMessageModal()" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
            
            <div class="p-6">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Xabar matni:</label>
                        <textarea id="bulkMessageText" rows="4" 
                                  class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                  placeholder="Xabar matnini kiriting..."></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tanlangan foydalanuvchilar:</label>
                        <span id="selectedUsersCount" class="text-sm text-gray-500">0 ta foydalanuvchi tanlandi</span>
                    </div>
                    <button onclick="sendBulkMessage()" class="w-full p-3 bg-purple-600 text-white rounded-lg hover:bg-purple-700">
                        <i class="fas fa-paper-plane mr-2"></i>Xabar yuborish
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Single User Message Modal -->
<div id="singleMessageModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-xl shadow-xl max-w-lg w-full">
            <div class="p-6 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-800">Xabar yuborish</h3>
                    <button onclick="hideSingleMessageModal()" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
            
            <div class="p-6">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Foydalanuvchi:</label>
                        <span id="selectedUserName" class="text-sm text-gray-500"></span>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Xabar matni:</label>
                        <textarea id="singleMessageText" rows="4" 
                                  class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                  placeholder="Xabar matnini kiriting..."></textarea>
                    </div>
                    <button onclick="sendSingleMessage()" class="w-full p-3 bg-purple-600 text-white rounded-lg hover:bg-purple-700">
                        <i class="fas fa-paper-plane mr-2"></i>Xabar yuborish
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let selectedUserId = null;
let selectedRestaurantId = null;

function filterUsers() {
    const restaurantId = document.getElementById('restaurantFilter').value;
    const search = document.getElementById('searchFilter').value;
    const status = document.getElementById('statusFilter').value;
    
    const url = new URL(window.location);
    if (restaurantId) url.searchParams.set('restaurant_id', restaurantId);
    if (search) url.searchParams.set('search', search);
    if (status) url.searchParams.set('status', status);
    
    window.location.href = url.toString();
}

function toggleSelectAll() {
    const selectAll = document.getElementById('selectAll');
    const checkboxes = document.querySelectorAll('.user-checkbox');
    
    checkboxes.forEach(checkbox => {
        checkbox.checked = selectAll.checked;
    });
    
    updateSelectedCount();
}

function selectAllUsers() {
    const checkboxes = document.querySelectorAll('.user-checkbox');
    checkboxes.forEach(checkbox => {
        checkbox.checked = true;
    });
    document.getElementById('selectAll').checked = true;
    updateSelectedCount();
}

function updateSelectedCount() {
    const checkboxes = document.querySelectorAll('.user-checkbox:checked');
    document.getElementById('selectedUsersCount').textContent = `${checkboxes.length} ta foydalanuvchi tanlandi`;
}

function showBulkMessageModal() {
    const checkboxes = document.querySelectorAll('.user-checkbox:checked');
    if (checkboxes.length === 0) {
        alert('Iltimos, kamida bitta foydalanuvchini tanlang');
        return;
    }
    document.getElementById('bulkMessageModal').classList.remove('hidden');
}

function hideBulkMessageModal() {
    document.getElementById('bulkMessageModal').classList.add('hidden');
}

function sendBulkMessage() {
    const checkboxes = document.querySelectorAll('.user-checkbox:checked');
    const message = document.getElementById('bulkMessageText').value;
    
    if (!message.trim()) {
        alert('Iltimos, xabar matnini kiriting');
        return;
    }
    
    const userIds = Array.from(checkboxes).map(cb => parseInt(cb.value));
    const restaurantIds = [...new Set(Array.from(checkboxes).map(cb => parseInt(cb.dataset.restaurant)))];
    
    // Group users by restaurant and send messages
    const promises = restaurantIds.map(restaurantId => {
        const restaurantUserIds = Array.from(checkboxes)
            .filter(cb => parseInt(cb.dataset.restaurant) === restaurantId)
            .map(cb => parseInt(cb.value));
        
        return fetch('{{ route("admin.bots.send-multiple") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                restaurant_ids: [restaurantId],
                user_ids: restaurantUserIds,
                message: message
            })
        }).then(response => response.json());
    });
    
    Promise.all(promises).then(results => {
        let message = 'Xabar yuborish natijalari:\n\n';
        let totalSuccess = 0;
        let totalErrors = 0;
        
        results.forEach(result => {
            if (result.success) {
                result.results.forEach(r => {
                    if (r.success) {
                        message += `${r.restaurant_name}: ✅ ${r.success_count} ta yuborildi, ${r.error_count} ta xatolik\n`;
                        totalSuccess += r.success_count;
                        totalErrors += r.error_count;
                    } else {
                        message += `${r.restaurant_name}: ❌ ${r.message}\n`;
                        totalErrors++;
                    }
                });
            }
        });
        
        message += `\nJami: ${totalSuccess} ta yuborildi, ${totalErrors} ta xatolik`;
        alert(message);
        hideBulkMessageModal();
    }).catch(error => {
        console.error('Error sending messages:', error);
        alert('Xatolik yuz berdi');
    });
}

function sendMessageToUser(userId, restaurantId) {
    selectedUserId = userId;
    selectedRestaurantId = restaurantId;
    
    // Find user name
    const row = document.querySelector(`input[value="${userId}"]`).closest('tr');
    const nameElement = row.querySelector('.text-sm.font-medium');
    const userName = nameElement ? nameElement.textContent : 'Foydalanuvchi';
    
    document.getElementById('selectedUserName').textContent = userName;
    document.getElementById('singleMessageModal').classList.remove('hidden');
}

function hideSingleMessageModal() {
    document.getElementById('singleMessageModal').classList.add('hidden');
    selectedUserId = null;
    selectedRestaurantId = null;
}

function sendSingleMessage() {
    const message = document.getElementById('singleMessageText').value;
    
    if (!message.trim()) {
        alert('Iltimos, xabar matnini kiriting');
        return;
    }
    
    fetch('{{ route("admin.bots.send-multiple") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            restaurant_ids: [parseInt(selectedRestaurantId)],
            user_ids: [selectedUserId],
            message: message
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Xabar muvaffaqiyatli yuborildi!');
            hideSingleMessageModal();
        } else {
            alert('Xatolik: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error sending message:', error);
        alert('Xatolik yuz berdi');
    });
}

// Update selected count when checkboxes change
document.addEventListener('DOMContentLoaded', function() {
    const checkboxes = document.querySelectorAll('.user-checkbox');
    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateSelectedCount);
    });
    updateSelectedCount();
});
</script>
@endsection 