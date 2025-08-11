@extends('admin.layouts.app')

@section('title', 'Bot sozlamalari')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Bot sozlamalari</h1>
            <p class="text-gray-600">Telegram botlarni boshqarish</p>
        </div>
        <div class="flex items-center space-x-3">
            <button onclick="showBulkOperations()" 
                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                <i class="fas fa-cogs mr-2"></i>Bulk operatsiyalar
            </button>
            <a href="{{ route('admin.bots.all-users') }}" 
               class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors">
                <i class="fas fa-users mr-2"></i>Barcha foydalanuvchilar
            </a>
            <a href="{{ route('admin.global-telegram.index') }}" 
               class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                <i class="fas fa-globe mr-2"></i>Global foydalanuvchilar
            </a>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-robot text-blue-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-500">Jami botlar</p>
                    <p class="text-2xl font-bold text-gray-800" id="total-bots">{{ $restaurants->count() }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-check text-green-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-500">Faol botlar</p>
                    <p class="text-2xl font-bold text-gray-800" id="active-bots">-</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-users text-purple-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-500">Jami foydalanuvchilar</p>
                    <p class="text-2xl font-bold text-gray-800" id="total-users">-</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-comments text-orange-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-500">Jami xabarlar</p>
                    <p class="text-2xl font-bold text-gray-800" id="total-messages">-</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Bot Settings Grid -->
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
                        <input type="checkbox" class="bot-checkbox" value="{{ $restaurant->id }}" 
                               onchange="updateBulkSelection()">
                        @if($restaurant->bot_token && $restaurant->bot_username)
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
                        <i class="fab fa-telegram w-4 mr-2"></i>
                        <span>
                            @if($restaurant->bot_username)
                                @{{ $restaurant->bot_username }}
                            @else
                                <span class="text-red-500">O'rnatilmagan</span>
                            @endif
                        </span>
                    </div>
                    <div class="flex items-center text-sm text-gray-600">
                        <i class="fas fa-key w-4 mr-2"></i>
                        <span>
                            @if($restaurant->bot_token)
                                <span class="text-green-500">O'rnatilgan</span>
                            @else
                                <span class="text-red-500">O'rnatilmagan</span>
                            @endif
                        </span>
                    </div>
                    <div class="flex items-center text-sm text-gray-600">
                        <i class="fas fa-users w-4 mr-2"></i>
                        <span>{{ $restaurant->telegramUsers()->count() }} foydalanuvchi</span>
                    </div>
                </div>

                <div class="flex items-center justify-between pt-4 border-t border-gray-100">
                    <div class="flex items-center space-x-2">
                        <a href="{{ route('admin.bots.users', $restaurant) }}" 
                           class="p-2 text-purple-600 hover:bg-purple-50 rounded-lg transition-colors" 
                           title="Foydalanuvchilar">
                            <i class="fas fa-users"></i>
                        </a>
                        <a href="{{ route('admin.bots.show', $restaurant) }}" 
                           class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors"
                           title="Sozlamalar">
                            <i class="fas fa-cog"></i>
                        </a>
                        <button onclick="testBot({{ $restaurant->id }})" 
                                class="p-2 text-green-600 hover:bg-green-50 rounded-lg transition-colors"
                                title="Test qilish">
                            <i class="fas fa-play"></i>
                        </button>
                    </div>
                    <span class="text-xs text-gray-500">{{ $restaurant->created_at->format('d.m.Y') }}</span>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Bulk Operations Modal -->
    <div id="bulkModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-xl shadow-xl max-w-2xl w-full">
                <div class="p-6 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-gray-800">Bulk operatsiyalar</h3>
                        <button onclick="hideBulkOperations()" class="text-gray-400 hover:text-gray-600">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
                
                <div class="p-6 space-y-6">
                    <!-- Bot Selection -->
                    <div>
                        <h4 class="font-medium text-gray-800 mb-3">Botlarni tanlang:</h4>
                        <div class="grid grid-cols-2 gap-3 max-h-40 overflow-y-auto">
                            @foreach($restaurants as $restaurant)
                            <label class="flex items-center space-x-2 cursor-pointer">
                                <input type="checkbox" class="bulk-bot-checkbox" value="{{ $restaurant->id }}">
                                <span class="text-sm">{{ $restaurant->name }}</span>
                            </label>
                            @endforeach
                        </div>
                    </div>

                    <!-- Operation Selection -->
                    <div>
                        <h4 class="font-medium text-gray-800 mb-3">Operatsiyani tanlang:</h4>
                        <div class="space-y-3">
                            <button onclick="bulkTestBots()" class="w-full p-3 bg-green-600 text-white rounded-lg hover:bg-green-700">
                                <i class="fas fa-play mr-2"></i>Barcha botlarni test qilish
                            </button>
                            <button onclick="bulkSetWebhooks()" class="w-full p-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                                <i class="fas fa-link mr-2"></i>Webhooklarni o'rnatish
                            </button>
                            <button onclick="showBulkMessageForm()" class="w-full p-3 bg-purple-600 text-white rounded-lg hover:bg-purple-700">
                                <i class="fas fa-paper-plane mr-2"></i>Xabar yuborish
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bulk Message Modal -->
    <div id="bulkMessageModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-xl shadow-xl max-w-lg w-full">
                <div class="p-6 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-gray-800">Xabar yuborish</h3>
                        <button onclick="hideBulkMessageForm()" class="text-gray-400 hover:text-gray-600">
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
                            <label class="block text-sm font-medium text-gray-700 mb-2">Foydalanuvchilar:</label>
                            <select id="bulkMessageUsers" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">Barcha faol foydalanuvchilar</option>
                                <option value="selected">Tanlangan foydalanuvchilar</option>
                            </select>
                        </div>
                        <button onclick="sendBulkMessage()" class="w-full p-3 bg-purple-600 text-white rounded-lg hover:bg-purple-700">
                            <i class="fas fa-paper-plane mr-2"></i>Xabar yuborish
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Load statistics on page load
document.addEventListener('DOMContentLoaded', function() {
    loadStatistics();
});

function loadStatistics() {
    fetch('{{ route("admin.bots.all-stats") }}')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('active-bots').textContent = data.stats.filter(s => s.is_active).length;
                document.getElementById('total-users').textContent = data.totals.users;
                document.getElementById('total-messages').textContent = data.totals.messages;
            }
        })
        .catch(error => console.error('Error loading statistics:', error));
}

function showBulkOperations() {
    document.getElementById('bulkModal').classList.remove('hidden');
}

function hideBulkOperations() {
    document.getElementById('bulkModal').classList.add('hidden');
}

function showBulkMessageForm() {
    hideBulkOperations();
    document.getElementById('bulkMessageModal').classList.remove('hidden');
}

function hideBulkMessageForm() {
    document.getElementById('bulkMessageModal').classList.add('hidden');
}

function updateBulkSelection() {
    const checkboxes = document.querySelectorAll('.bot-checkbox');
    const bulkCheckboxes = document.querySelectorAll('.bulk-bot-checkbox');
    
    checkboxes.forEach((checkbox, index) => {
        bulkCheckboxes[index].checked = checkbox.checked;
    });
}

function getSelectedBots() {
    const checkboxes = document.querySelectorAll('.bulk-bot-checkbox:checked');
    return Array.from(checkboxes).map(cb => parseInt(cb.value));
}

function bulkTestBots() {
    const selectedBots = getSelectedBots();
    if (selectedBots.length === 0) {
        alert('Iltimos, kamida bitta botni tanlang');
        return;
    }
    
    fetch('{{ route("admin.bots.test-multiple") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ restaurant_ids: selectedBots })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            let message = 'Test natijalari:\n\n';
            data.results.forEach(result => {
                message += `${result.restaurant_name}: ${result.success ? '✅' : '❌'} ${result.message}\n`;
            });
            alert(message);
        }
    })
    .catch(error => {
        console.error('Error testing bots:', error);
        alert('Xatolik yuz berdi');
    });
}

function bulkSetWebhooks() {
    const selectedBots = getSelectedBots();
    if (selectedBots.length === 0) {
        alert('Iltimos, kamida bitta botni tanlang');
        return;
    }
    
    if (!confirm('Tanlangan botlar uchun webhook o\'rnatishni xohlaysizmi?')) {
        return;
    }
    
    fetch('{{ route("admin.bots.set-webhooks-multiple") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ restaurant_ids: selectedBots })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            let message = 'Webhook natijalari:\n\n';
            data.results.forEach(result => {
                message += `${result.restaurant_name}: ${result.success ? '✅' : '❌'} ${result.message}\n`;
            });
            alert(message);
        }
    })
    .catch(error => {
        console.error('Error setting webhooks:', error);
        alert('Xatolik yuz berdi');
    });
}

function sendBulkMessage() {
    const selectedBots = getSelectedBots();
    const message = document.getElementById('bulkMessageText').value;
    const userType = document.getElementById('bulkMessageUsers').value;
    
    if (selectedBots.length === 0) {
        alert('Iltimos, kamida bitta botni tanlang');
        return;
    }
    
    if (!message.trim()) {
        alert('Iltimos, xabar matnini kiriting');
        return;
    }
    
    const data = {
        restaurant_ids: selectedBots,
        message: message
    };
    
    if (userType === 'selected') {
        // For now, we'll send to all active users
        // You can implement user selection later
        data.user_ids = [];
    }
    
    fetch('{{ route("admin.bots.send-multiple") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            let message = 'Xabar yuborish natijalari:\n\n';
            data.results.forEach(result => {
                if (result.success) {
                    message += `${result.restaurant_name}: ✅ ${result.success_count} ta yuborildi, ${result.error_count} ta xatolik\n`;
                } else {
                    message += `${result.restaurant_name}: ❌ ${result.message}\n`;
                }
            });
            message += `\nJami: ${data.summary.total_success} ta yuborildi, ${data.summary.total_errors} ta xatolik`;
            alert(message);
            hideBulkMessageForm();
        }
    })
    .catch(error => {
        console.error('Error sending messages:', error);
        alert('Xatolik yuz berdi');
    });
}

function testBot(restaurantId) {
    fetch(`/admin/bots/${restaurantId}/test`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Bot muvaffaqiyatli test qilindi!');
        } else {
            alert('Xatolik: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error testing bot:', error);
        alert('Xatolik yuz berdi');
    });
}
</script>
@endsection 