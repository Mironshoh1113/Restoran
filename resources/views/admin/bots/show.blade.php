@extends('admin.layouts.app')

@section('title', 'Bot sozlamalari')

@section('content')
<div class="max-w-6xl mx-auto space-y-6">
    <!-- Header -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <div class="w-16 h-16 bg-gradient-to-r from-blue-500 to-purple-600 rounded-xl flex items-center justify-center">
                    <i class="fas fa-robot text-white text-2xl"></i>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">{{ $restaurant->name }}</h1>
                    <p class="text-gray-600">Telegram Bot Boshqaruvi</p>
                </div>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('admin.bots.users', $restaurant) }}" 
                   class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg flex items-center space-x-2 transition-colors">
                    <i class="fas fa-users"></i>
                    <span>Foydalanuvchilar</span>
                </a>
                <a href="{{ route('admin.bots.index') }}" 
                   class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg flex items-center space-x-2 transition-colors">
                    <i class="fas fa-arrow-left"></i>
                    <span>Orqaga</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Bot Status Overview -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Bot Info -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center space-x-3 mb-4">
                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-info-circle text-blue-600"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-800">Bot Ma'lumotlari</h3>
            </div>
            
            @if($botInfo && $botInfo['ok'])
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Nomi:</span>
                        <span class="font-medium">{{ $botInfo['result']['first_name'] }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Username:</span>
                        <span class="font-medium">@{{ $botInfo['result']['username'] }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Holat:</span>
                        <span class="px-2 py-1 bg-green-100 text-green-800 text-xs rounded-full">Faol</span>
                    </div>
                </div>
            @else
                <div class="text-center py-4">
                    <i class="fas fa-exclamation-triangle text-yellow-500 text-2xl mb-2"></i>
                    <p class="text-gray-600">Bot ma'lumotlari yuklanmadi</p>
                    <p class="text-sm text-gray-500">Bot token ni tekshiring</p>
                </div>
            @endif
        </div>

        <!-- Webhook Status -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center space-x-3 mb-4">
                <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-link text-green-600"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-800">Webhook Holati</h3>
            </div>
            
            @if($webhookInfo && $webhookInfo['ok'])
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Holat:</span>
                        <span class="px-2 py-1 {{ $webhookInfo['result']['url'] ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }} text-xs rounded-full">
                            {{ $webhookInfo['result']['url'] ? 'Faol' : 'Faol emas' }}
                        </span>
                    </div>
                    @if($webhookInfo['result']['url'])
                        <div class="text-xs text-gray-600 break-all">
                            {{ $webhookInfo['result']['url'] }}
                        </div>
                    @endif
                    @if(isset($webhookInfo['result']['last_error_message']))
                        <div class="text-xs text-red-600">
                            Xatolik: {{ $webhookInfo['result']['last_error_message'] }}
                        </div>
                    @endif
                </div>
            @else
                <div class="text-center py-4">
                    <i class="fas fa-unlink text-red-500 text-2xl mb-2"></i>
                    <p class="text-gray-600">Webhook o'rnatilmagan</p>
                </div>
            @endif
        </div>

        <!-- Quick Stats -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center space-x-3 mb-4">
                <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-chart-bar text-purple-600"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-800">Statistika</h3>
            </div>
            
            <div class="space-y-3">
                <div class="flex justify-between">
                    <span class="text-gray-600">Foydalanuvchilar:</span>
                    <span class="font-medium">{{ $userCount ?? 0 }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Xabarlar:</span>
                    <span class="font-medium">{{ $messageCount ?? 0 }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Buyurtmalar:</span>
                    <span class="font-medium">{{ $orderCount ?? 0 }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Actions -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-6">Asosiy Amallar</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Webhook Setup -->
            <button onclick="setupWebhookAuto()" 
                    class="p-4 bg-green-50 hover:bg-green-100 border border-green-200 rounded-lg transition-colors group">
                <div class="text-center">
                    <i class="fas fa-magic text-green-600 text-2xl mb-2 group-hover:scale-110 transition-transform"></i>
                    <h4 class="font-medium text-gray-800">Webhook O'rnatish</h4>
                    <p class="text-sm text-gray-600">Avtomatik sozlash</p>
                </div>
            </button>

            <!-- Test Bot -->
            <button onclick="testBot()" 
                    class="p-4 bg-blue-50 hover:bg-blue-100 border border-blue-200 rounded-lg transition-colors group">
                <div class="text-center">
                    <i class="fas fa-play text-blue-600 text-2xl mb-2 group-hover:scale-110 transition-transform"></i>
                    <h4 class="font-medium text-gray-800">Bot Test</h4>
                    <p class="text-sm text-gray-600">Ishlashini tekshirish</p>
                </div>
            </button>

            <!-- View Users -->
            <a href="{{ route('admin.bots.users', $restaurant) }}" 
               class="p-4 bg-purple-50 hover:bg-purple-100 border border-purple-200 rounded-lg transition-colors group">
                <div class="text-center">
                    <i class="fas fa-users text-purple-600 text-2xl mb-2 group-hover:scale-110 transition-transform"></i>
                    <h4 class="font-medium text-gray-800">Foydalanuvchilar</h4>
                    <p class="text-sm text-gray-600">Ro'yxatni ko'rish</p>
                </div>
            </a>

            <!-- Settings -->
            <a href="{{ route('admin.restaurants.edit', $restaurant) }}" 
               class="p-4 bg-gray-50 hover:bg-gray-100 border border-gray-200 rounded-lg transition-colors group">
                <div class="text-center">
                    <i class="fas fa-cog text-gray-600 text-2xl mb-2 group-hover:scale-110 transition-transform"></i>
                    <h4 class="font-medium text-gray-800">Sozlamalar</h4>
                    <p class="text-sm text-gray-600">Bot sozlamalari</p>
                </div>
            </a>
        </div>
    </div>

    <!-- Bot Configuration -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-6">Bot Konfiguratsiyasi</h3>
        
        <form id="botConfigForm" class="space-y-6">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Bot Token -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Bot Token</label>
                    <div class="relative">
                        <input type="password" id="bot_token" name="bot_token" 
                               value="{{ $restaurant->bot_token }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                               placeholder="1234567890:ABCdefGHIjklMNOpqrsTUVwxyz">
                        <button type="button" onclick="toggleTokenVisibility()" 
                                class="absolute right-3 top-2.5 text-gray-400 hover:text-gray-600">
                            <i class="fas fa-eye" id="tokenEye"></i>
                        </button>
                    </div>
                    <p class="text-xs text-gray-500 mt-1">BotFather dan olingan token</p>
                </div>

                <!-- Webhook URL -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Webhook URL</label>
                    <div class="flex space-x-2">
                        <input type="url" id="webhook_url" name="webhook_url" 
                               value="{{ url('/api/telegram-webhook/' . ($restaurant->bot_token ?? '')) }}"
                               class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-gray-50"
                               readonly>
                        <button type="button" onclick="copyWebhookUrl()" 
                                class="px-3 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">
                            <i class="fas fa-copy"></i>
                        </button>
                    </div>
                    <p class="text-xs text-gray-500 mt-1">Avtomatik yaratilgan URL</p>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex space-x-4 pt-4 border-t">
                <button type="button" onclick="saveConfiguration()" 
                        class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">
                    <i class="fas fa-save mr-2"></i>Saqlash
                </button>
                <button type="button" onclick="setupWebhookAuto()" 
                        class="px-6 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition-colors">
                    <i class="fas fa-magic mr-2"></i>Webhook O'rnatish
                </button>
                <button type="button" onclick="deleteWebhook()" 
                        class="px-6 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-colors">
                    <i class="fas fa-trash mr-2"></i>Webhook O'chirish
                </button>
            </div>
        </form>
    </div>

    <!-- Web App URLs -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-6">Web App URL lari</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Enhanced Web App -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Enhanced Web App (Tavsiya etiladi)</label>
                <div class="flex space-x-2">
                    <input type="url" value="{{ url('/enhanced-web-interface?bot_token=' . ($restaurant->bot_token ?? '')) }}"
                           class="flex-1 px-3 py-2 border border-gray-300 rounded-lg bg-gray-50 text-sm" readonly>
                    <button onclick="copyUrl(this.previousElementSibling.value)" 
                            class="px-3 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition-colors">
                        <i class="fas fa-copy"></i>
                    </button>
                </div>
                <p class="text-xs text-gray-500 mt-1">Yaxshilangan dizayn va funksiyalar</p>
            </div>

            <!-- Basic Web App -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Basic Web App</label>
                <div class="flex space-x-2">
                    <input type="url" value="{{ url('/web-interface?bot_token=' . ($restaurant->bot_token ?? '')) }}"
                           class="flex-1 px-3 py-2 border border-gray-300 rounded-lg bg-gray-50 text-sm" readonly>
                    <button onclick="copyUrl(this.previousElementSibling.value)" 
                            class="px-3 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">
                        <i class="fas fa-copy"></i>
                    </button>
                </div>
                <p class="text-xs text-gray-500 mt-1">Asosiy web interface</p>
            </div>
        </div>
    </div>
</div>

<!-- Success/Error Messages -->
<div id="notification" class="fixed top-4 right-4 z-50 hidden">
    <div class="bg-white border border-gray-200 rounded-lg shadow-lg p-4 max-w-sm">
        <div class="flex items-center space-x-3">
            <div id="notificationIcon" class="flex-shrink-0"></div>
            <div id="notificationMessage" class="text-sm text-gray-800"></div>
        </div>
    </div>
</div>

<script>
// Global variables
const restaurantId = {{ $restaurant->id }};
const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

// Setup webhook automatically
function setupWebhookAuto() {
    showNotification('Webhook o\'rnatilmoqda...', 'info');
    
    fetch(`/admin/bots/${restaurantId}/webhook/auto`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('✅ ' + data.message, 'success');
            setTimeout(() => location.reload(), 2000);
        } else {
            showNotification('❌ ' + data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('❌ Xatolik yuz berdi', 'error');
    });
}

// Test bot
function testBot() {
    showNotification('Bot test qilinmoqda...', 'info');
    
    fetch(`/admin/bots/${restaurantId}/send-test`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Content-Type': 'application/json'
        }
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
        showNotification('❌ Test xatolik', 'error');
    });
}

// Delete webhook
function deleteWebhook() {
    if (!confirm('Webhook ni o\'chirmoqchimisiz?')) return;
    
    showNotification('Webhook o\'chirilmoqda...', 'info');
    
    fetch(`/admin/bots/${restaurantId}/webhook`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('✅ ' + data.message, 'success');
            setTimeout(() => location.reload(), 2000);
        } else {
            showNotification('❌ ' + data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('❌ Xatolik yuz berdi', 'error');
    });
}

// Save configuration
function saveConfiguration() {
    const botToken = document.getElementById('bot_token').value;
    
    if (!botToken) {
        showNotification('❌ Bot token ni kiriting', 'error');
        return;
    }
    
    showNotification('Sozlamalar saqlanmoqda...', 'info');
    
    fetch(`/admin/restaurants/${restaurantId}`, {
        method: 'PUT',
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            bot_token: botToken
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('✅ Sozlamalar saqlandi', 'success');
            // Update webhook URL
            document.getElementById('webhook_url').value = `{{ url('/api/telegram-webhook/') }}/${botToken}`;
        } else {
            showNotification('❌ Saqlashda xatolik', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('❌ Xatolik yuz berdi', 'error');
    });
}

// Toggle token visibility
function toggleTokenVisibility() {
    const tokenInput = document.getElementById('bot_token');
    const eyeIcon = document.getElementById('tokenEye');
    
    if (tokenInput.type === 'password') {
        tokenInput.type = 'text';
        eyeIcon.className = 'fas fa-eye-slash';
    } else {
        tokenInput.type = 'password';
        eyeIcon.className = 'fas fa-eye';
    }
}

// Copy webhook URL
function copyWebhookUrl() {
    const webhookUrl = document.getElementById('webhook_url').value;
    copyUrl(webhookUrl);
}

// Copy URL to clipboard
function copyUrl(url) {
    navigator.clipboard.writeText(url).then(() => {
        showNotification('✅ URL nusxalandi', 'success');
    }).catch(err => {
        console.error('Error copying URL:', err);
        showNotification('❌ Nusxalashda xatolik', 'error');
    });
}

// Show notification
function showNotification(message, type = 'info') {
    const notification = document.getElementById('notification');
    const icon = document.getElementById('notificationIcon');
    const messageEl = document.getElementById('notificationMessage');
    
    // Set icon and color based on type
    let iconClass = '';
    let bgColor = '';
    
    switch (type) {
        case 'success':
            iconClass = 'fas fa-check-circle text-green-600';
            bgColor = 'bg-green-50 border-green-200';
            break;
        case 'error':
            iconClass = 'fas fa-exclamation-circle text-red-600';
            bgColor = 'bg-red-50 border-red-200';
            break;
        case 'info':
            iconClass = 'fas fa-info-circle text-blue-600';
            bgColor = 'bg-blue-50 border-blue-200';
            break;
    }
    
    icon.className = iconClass;
    notification.className = `fixed top-4 right-4 z-50 ${bgColor} border rounded-lg shadow-lg p-4 max-w-sm`;
    messageEl.textContent = message;
    
    notification.classList.remove('hidden');
    
    // Auto hide after 5 seconds
    setTimeout(() => {
        notification.classList.add('hidden');
    }, 5000);
}

// Auto-update webhook URL when bot token changes
document.getElementById('bot_token').addEventListener('input', function() {
    const botToken = this.value;
    if (botToken) {
        document.getElementById('webhook_url').value = `{{ url('/api/telegram-webhook/') }}/${botToken}`;
    }
});
</script>
@endsection 