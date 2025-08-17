@extends('admin.layouts.app')

@section('title', 'Telegram foydalanuvchilari')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-200">{{ $restaurant->name }} - Telegram foydalanuvchilari</h1>
            <p class="text-gray-600 dark:text-gray-400">Bot orqali murojaat qilgan foydalanuvchilar</p>
        </div>
        <div class="flex items-center space-x-3">
            <button onclick="getUsersStats()" 
                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center space-x-2 transition-colors">
                <i class="fas fa-chart-bar"></i>
                <span>Statistika</span>
            </button>
            <a href="{{ route('admin.bots.show', $restaurant) }}" 
               class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg flex items-center space-x-2 transition-colors">
                <i class="fas fa-arrow-left"></i>
                <span>Orqaga</span>
            </a>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div id="statsCards" class="grid grid-cols-1 md:grid-cols-4 gap-6 hidden">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center space-x-3">
                <div class="w-12 h-12 bg-blue-500 rounded-xl flex items-center justify-center">
                    <i class="fas fa-users text-white text-xl"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Jami foydalanuvchilar</p>
                    <p class="text-2xl font-bold text-gray-800 dark:text-gray-200" id="totalUsers">0</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center space-x-3">
                <div class="w-12 h-12 bg-green-500 rounded-xl flex items-center justify-center">
                    <i class="fas fa-user-check text-white text-xl"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Faol foydalanuvchilar</p>
                    <p class="text-2xl font-bold text-gray-800 dark:text-gray-200" id="activeUsers">0</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center space-x-3">
                <div class="w-12 h-12 bg-yellow-500 rounded-xl flex items-center justify-center">
                    <i class="fas fa-clock text-white text-xl"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">So'nggi 7 kun</p>
                    <p class="text-2xl font-bold text-gray-800 dark:text-gray-200" id="recentUsers">0</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center space-x-3">
                <div class="w-12 h-12 bg-purple-500 rounded-xl flex items-center justify-center">
                    <i class="fas fa-calendar-day text-white text-xl"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Bugun</p>
                    <p class="text-2xl font-bold text-gray-800 dark:text-gray-200" id="todayUsers">0</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Message Sending Section -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Xabar yuborish</h3>
        </div>
        
        <div class="p-6 space-y-4">
            <div class="flex items-center space-x-4">
                <button onclick="showSendToAllModal()" 
                        class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition-colors flex items-center space-x-2">
                    <i class="fas fa-broadcast-tower"></i>
                    <span>Barchaga xabar yuborish</span>
                </button>
                <button onclick="showSendToSelectedModal()" 
                        class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors flex items-center space-x-2">
                    <i class="fas fa-user-friends"></i>
                    <span>Tanlanganlarga xabar yuborish</span>
                </button>
                <button onclick="testModal()" 
                        class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg transition-colors flex items-center space-x-2">
                    <i class="fas fa-bug"></i>
                    <span>Test Modal</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Users List -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Foydalanuvchilar ro'yxati</h3>
                <div class="flex items-center space-x-2">
                    <input type="text" id="searchUsers" placeholder="Foydalanuvchi qidirish..." 
                           class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                </div>
            </div>
        </div>
        
        <div class="p-6">
            @if($users->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    <input type="checkbox" id="selectAll" class="rounded border-gray-300 dark:border-gray-600">
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Foydalanuvchi
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Username
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Holat
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    So'nggi faollik
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Amallar
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach($users as $user)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <input type="checkbox" class="user-checkbox rounded border-gray-300 dark:border-gray-600" value="{{ $user->telegram_id }}">
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center">
                                            <span class="text-white font-semibold text-sm">{{ substr($user->first_name ?? 'U', 0, 1) }}</span>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                                {{ $user->full_name }}
                                            </div>
                                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                                ID: {{ $user->telegram_id }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-sm text-gray-900 dark:text-gray-100">
                                        {{ $user->username ? '@' . $user->username : 'Yo\'q' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($user->is_active)
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 dark:bg-green-900/20 text-green-800 dark:text-green-200">
                                            <i class="fas fa-check mr-1"></i>Faol
                                        </span>
                                    @else
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 dark:bg-red-900/20 text-red-800 dark:text-red-200">
                                            <i class="fas fa-times mr-1"></i>Faol emas
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                    {{ $user->last_activity ? $user->last_activity->diffForHumans() : 'Noma\'lum' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <a href="{{ route('admin.bots.conversation', [$restaurant, $user]) }}" 
                                       class="text-purple-600 hover:text-purple-900 dark:text-purple-400 dark:hover:text-purple-300 mr-3"
                                       title="Xabar almashinuvi">
                                        <i class="fas fa-comments"></i>
                                    </a>
                                    <button onclick="sendMessageToUser({{ $user->telegram_id }})" 
                                            class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300 mr-3"
                                            title="Xabar yuborish">
                                        <i class="fas fa-paper-plane"></i>
                                    </button>
                                    <button onclick="viewUserDetails({{ $user->id }})" 
                                            class="text-green-600 hover:text-green-900 dark:text-green-400 dark:hover:text-green-300"
                                            title="Ma'lumotlar">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                <div class="mt-6">
                    {{ $users->links() }}
                </div>
            @else
                <div class="text-center py-8">
                    <i class="fas fa-users text-gray-400 dark:text-gray-500 text-4xl mb-4"></i>
                    <p class="text-gray-500 dark:text-gray-400">Hali hech qanday foydalanuvchi yo'q</p>
                    <p class="text-sm text-gray-400 dark:text-gray-500 mt-2">Bot orqali murojaat qilgan foydalanuvchilar bu yerda ko'rinadi</p>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Send to All Users Modal -->
<div id="sendToAllModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-white dark:bg-gray-800 rounded-lg p-6 w-full max-w-md mx-4">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Barchaga xabar yuborish</h3>
            <button onclick="closeSendToAllModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300" title="Yopish">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <div class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Xabar</label>
                <textarea id="sendToAllMessage" rows="4" 
                          class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                          placeholder="Xabar matnini kiriting..."></textarea>
            </div>
            
            <div class="flex items-center justify-end space-x-3">
                <button onclick="closeSendToAllModal()" 
                        class="px-4 py-2 text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200">
                    Bekor qilish
                </button>
                <button onclick="sendToAllUsers()" 
                        class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition-colors">
                    Yuborish
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Send to Selected Users Modal -->
<div id="sendToSelectedModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-white dark:bg-gray-800 rounded-lg p-6 w-full max-w-md mx-4">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Tanlanganlarga xabar yuborish</h3>
            <button onclick="closeSendToSelectedModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300" title="Yopish">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <div class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Xabar</label>
                <textarea id="sendToSelectedMessage" rows="4" 
                          class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                          placeholder="Xabar matnini kiriting..."></textarea>
            </div>
            
            <div class="flex items-center justify-end space-x-3">
                <button onclick="closeSendToSelectedModal()" 
                        class="px-4 py-2 text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200">
                    Bekor qilish
                </button>
                <button onclick="sendToSelectedUsers()" 
                        class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">
                    Yuborish
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// Statistics
function getUsersStats() {
    fetch(`/admin/bots/{{ $restaurant->id }}/users/stats`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('totalUsers').textContent = data.stats.total_users;
                document.getElementById('activeUsers').textContent = data.stats.active_users;
                document.getElementById('recentUsers').textContent = data.stats.recent_users;
                document.getElementById('todayUsers').textContent = data.stats.today_users;
                document.getElementById('statsCards').classList.remove('hidden');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Statistikani yuklashda xatolik', 'error');
        });
}

// Select all functionality
document.getElementById('selectAll').addEventListener('change', function() {
    const checkboxes = document.querySelectorAll('.user-checkbox');
    checkboxes.forEach(checkbox => {
        checkbox.checked = this.checked;
    });
});

// Search functionality
document.getElementById('searchUsers').addEventListener('input', function() {
    const searchTerm = this.value.toLowerCase();
    const rows = document.querySelectorAll('tbody tr');
    
    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(searchTerm) ? '' : 'none';
    });
});

// Modal functions
function showSendToAllModal() {
    document.getElementById('sendToAllModal').classList.remove('hidden');
}

function closeSendToAllModal() {
    const modal = document.getElementById('sendToAllModal');
    if (modal) {
        modal.classList.add('hidden');
        // Clear the message field
        const messageField = document.getElementById('sendToAllMessage');
        if (messageField) {
            messageField.value = '';
        }
        console.log('Modal closed successfully');
    } else {
        console.error('Modal element not found');
    }
    
    // Force close with multiple methods
    try {
        const modalElement = document.getElementById('sendToAllModal');
        if (modalElement) {
            modalElement.style.display = 'none';
            modalElement.classList.add('hidden');
            modalElement.classList.remove('flex');
            console.log('Modal force closed with multiple methods');
        }
    } catch (e) {
        console.error('Error in force close:', e);
    }
}

function showSendToSelectedModal() {
    const selectedUsers = document.querySelectorAll('.user-checkbox:checked');
    if (selectedUsers.length === 0) {
        showNotification('Kamida bitta foydalanuvchi tanlang', 'warning');
        return;
    }
    document.getElementById('sendToSelectedModal').classList.remove('hidden');
}

function closeSendToSelectedModal() {
    const modal = document.getElementById('sendToSelectedModal');
    if (modal) {
        modal.classList.add('hidden');
        modal.style.display = 'none';
        modal.classList.remove('flex');
    }
    document.getElementById('sendToSelectedMessage').value = '';
    
    // Force close with multiple methods
    try {
        const modalElement = document.getElementById('sendToSelectedModal');
        if (modalElement) {
            modalElement.style.display = 'none';
            modalElement.classList.add('hidden');
            modalElement.classList.remove('flex');
            console.log('Selected modal force closed with multiple methods');
        }
    } catch (e) {
        console.error('Error in force close selected modal:', e);
    }
}

// Local notification function to ensure it works
function showNotification(message, type = 'success') {
    // Try to use global notification first
    if (typeof window.showNotification === 'function') {
        window.showNotification(message, type);
    } else if (typeof window.utils !== 'undefined' && typeof window.utils.showNotification === 'function') {
        window.utils.showNotification(message, type);
    } else {
        // Fallback to simple alert
        alert(message);
    }
}

// Test function to check if everything is working
function testModal() {
    console.log('Testing modal functionality...');
    showNotification('Test notification', 'info');
    showSendToAllModal();
}

// Send message functions
function sendToAllUsers() {
    const message = document.getElementById('sendToAllMessage').value.trim();
    if (!message) {
        showNotification('Xabar matnini kiriting', 'warning');
        return;
    }
    
    console.log('Sending message to all users:', message);
    
    // Show loading state
    const sendButton = document.querySelector('#sendToAllModal button[onclick="sendToAllUsers()"]');
    const originalText = sendButton.innerHTML;
    sendButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Yuborilmoqda...';
    sendButton.disabled = true;
    
    fetch(`/admin/bots/{{ $restaurant->id }}/users/send-all`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            message: message
        })
    })
    .then(response => {
        console.log('Response status:', response.status);
        return response.json();
    })
    .then(data => {
        console.log('Response data:', data);
        
        if (data.success) {
            showNotification('✅ ' + data.message, 'success');
            // Force close modal immediately
            setTimeout(() => {
                closeSendToAllModal();
                // Additional fallback close
                const modal = document.getElementById('sendToAllModal');
                if (modal) {
                    modal.style.display = 'none';
                    modal.classList.add('hidden');
                }
            }, 100);
            // Clear the message field
            document.getElementById('sendToAllMessage').value = '';
        } else {
            showNotification('❌ ' + (data.message || 'Xatolik yuz berdi'), 'error');
        }
    })
    .catch(error => {
        console.error('Fetch error:', error);
        showNotification('❌ Xatolik yuz berdi: ' + error.message, 'error');
    })
    .finally(() => {
        // Reset button state
        sendButton.innerHTML = originalText;
        sendButton.disabled = false;
    });
}

function sendToSelectedUsers() {
    const message = document.getElementById('sendToSelectedMessage').value.trim();
    if (!message) {
        showNotification('Xabar matnini kiriting', 'warning');
        return;
    }
    
    const selectedUsers = Array.from(document.querySelectorAll('.user-checkbox:checked'))
        .map(checkbox => parseInt(checkbox.value));
    
    if (selectedUsers.length === 0) {
        showNotification('Kamida bitta foydalanuvchi tanlang', 'warning');
        return;
    }
    
    // Show loading state
    const sendButton = document.querySelector('#sendToSelectedModal button[onclick="sendToSelectedUsers()"]');
    const originalText = sendButton.innerHTML;
    sendButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Yuborilmoqda...';
    sendButton.disabled = true;

    fetch(`/admin/bots/{{ $restaurant->id }}/users/send`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            user_ids: selectedUsers,
            message: message
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('✅ ' + data.message, 'success');
            // Force close modal immediately
            setTimeout(() => {
                closeSendToSelectedModal();
                // Additional fallback close
                const modal = document.getElementById('sendToSelectedModal');
                if (modal) {
                    modal.style.display = 'none';
                    modal.classList.add('hidden');
                }
            }, 100);
            // Uncheck all checkboxes
            document.querySelectorAll('.user-checkbox').forEach(checkbox => checkbox.checked = false);
            document.getElementById('selectAll').checked = false;
        } else {
            showNotification('❌ ' + data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Xatolik yuz berdi', 'error');
    })
    .finally(() => {
        // Reset button state
        sendButton.innerHTML = originalText;
        sendButton.disabled = false;
    });
}

function sendMessageToUser(telegramId) {
    const message = prompt('Xabar matnini kiriting:');
    if (!message) return;
    
    fetch(`/admin/bots/{{ $restaurant->id }}/users/send`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            user_ids: [telegramId],
            message: message
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('✅ ' + data.message, 'success');
        } else {
            showNotification('❌ ' + data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Xatolik yuz berdi', 'error');
    });
}

function viewUserDetails(userId) {
    // This can be expanded to show more user details
    showNotification('Foydalanuvchi ma\'lumotlari ko\'rsatiladi', 'info');
}

// Load stats on page load
document.addEventListener('DOMContentLoaded', function() {
    getUsersStats();
    
    // Add escape key handler for modals
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeSendToAllModal();
            closeSendToSelectedModal();
        }
    });
    
    // Add click outside to close for modals
    document.addEventListener('click', function(e) {
        const sendToAllModal = document.getElementById('sendToAllModal');
        const sendToSelectedModal = document.getElementById('sendToSelectedModal');
        
        if (e.target === sendToAllModal) {
            closeSendToAllModal();
        }
        if (e.target === sendToSelectedModal) {
            closeSendToSelectedModal();
        }
    });
});
</script>
@endsection 