@extends('admin.layouts.app')

@section('title', 'Bot sozlamalari')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">{{ $restaurant->name }} - Bot sozlamalari</h1>
            <p class="text-gray-600">Telegram bot sozlamalarini boshqarish</p>
        </div>
        <a href="{{ route('admin.bots.index') }}" 
           class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg flex items-center space-x-2 transition-colors">
            <i class="fas fa-arrow-left"></i>
            <span>Orqaga</span>
        </a>
    </div>

    <!-- Bot Status -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Bot Information -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Bot ma'lumotlari</h3>
            
            @if($botInfo && $botInfo['ok'])
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-gray-600">Bot nomi:</span>
                        <span class="font-medium">{{ $botInfo['result']['first_name'] }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-gray-600">Username:</span>
                        <span class="font-medium">@{{ $botInfo['result']['username'] }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-gray-600">Bot ID:</span>
                        <span class="font-medium">{{ $botInfo['result']['id'] }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-gray-600">Holat:</span>
                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                            <i class="fas fa-check mr-1"></i>Faol
                        </span>
                    </div>
                </div>
            @else
                <div class="text-center py-4">
                    <i class="fas fa-exclamation-triangle text-yellow-500 text-2xl mb-2"></i>
                    <p class="text-gray-600">Bot ma'lumotlari topilmadi</p>
                </div>
            @endif
        </div>

        <!-- Webhook Information -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Webhook ma'lumotlari</h3>
            
            @if($webhookInfo && $webhookInfo['ok'])
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-gray-600">Webhook URL:</span>
                        <span class="font-medium text-sm">{{ $webhookInfo['result']['url'] ?? 'O\'rnatilmagan' }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-gray-600">Holat:</span>
                        <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $webhookInfo['result']['url'] ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $webhookInfo['result']['url'] ? 'Faol' : 'Faol emas' }}
                        </span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-gray-600">Xatolar:</span>
                        <span class="font-medium">{{ $webhookInfo['result']['last_error_message'] ?? 'Yo\'q' }}</span>
                    </div>
                </div>
            @else
                <div class="text-center py-4">
                    <i class="fas fa-exclamation-triangle text-yellow-500 text-2xl mb-2"></i>
                    <p class="text-gray-600">Webhook ma'lumotlari topilmadi</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Bot Settings Form -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800">Bot sozlamalari</h3>
        </div>
        
        <form action="{{ route('admin.bots.update', $restaurant) }}" method="POST" class="p-6 space-y-4">
            @csrf
            @method('PATCH')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="bot_username" class="block text-sm font-medium text-gray-700 mb-2">Bot username</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500">@</span>
                        <input type="text" id="bot_username" name="bot_username" value="{{ old('bot_username', $restaurant->bot_username) }}"
                               class="w-full pl-8 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                               placeholder="bot_username">
                    </div>
                    @error('bot_username')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label for="bot_token" class="block text-sm font-medium text-gray-700 mb-2">Bot token</label>
                    <input type="text" id="bot_token" name="bot_token" value="{{ old('bot_token', $restaurant->bot_token) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           placeholder="1234567890:ABCdefGHIjklMNOpqrsTUVwxyz">
                    @error('bot_token')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-gray-500">BotFather dan olingan token</p>
                </div>
            </div>
            
            <div>
                <label for="webhook_url" class="block text-sm font-medium text-gray-700 mb-2">Webhook URL</label>
                <input type="url" id="webhook_url" name="webhook_url" value="{{ old('webhook_url', url('/telegram/webhook')) }}"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                       placeholder="https://example.com/telegram/webhook">
                @error('webhook_url')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
                <p class="mt-1 text-xs text-gray-500">Telegram webhook URL manzili</p>
            </div>
            
            <div class="flex items-center justify-end space-x-3 pt-4">
                <button type="button" onclick="testBot()" 
                        class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition-colors">
                    <i class="fas fa-play mr-2"></i>Test qilish
                </button>
                <button type="submit" 
                        class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors">
                    Saqlash
                </button>
            </div>
        </form>
    </div>

    <!-- Test Message -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800">Test xabar yuborish</h3>
        </div>
        
        <div class="p-6 space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="test_chat_id" class="block text-sm font-medium text-gray-700 mb-2">Chat ID</label>
                    <input type="number" id="test_chat_id" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           placeholder="123456789">
                </div>
                <div>
                    <label for="test_message" class="block text-sm font-medium text-gray-700 mb-2">Xabar</label>
                    <input type="text" id="test_message" value="Test xabar"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           placeholder="Test xabar">
                </div>
            </div>
            
            <button onclick="sendTestMessage()" 
                    class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg transition-colors">
                <i class="fas fa-paper-plane mr-2"></i>Xabar yuborish
            </button>
        </div>
    </div>

    <!-- Webhook Actions -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800">Webhook boshqaruvi</h3>
        </div>
        
        <div class="p-6 space-y-4">
            <div class="flex items-center space-x-4">
                <button onclick="setWebhook()" 
                        class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">
                    <i class="fas fa-link mr-2"></i>Webhook o'rnatish
                </button>
                <button onclick="deleteWebhook()" 
                        class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-colors">
                    <i class="fas fa-unlink mr-2"></i>Webhook o'chirish
                </button>
            </div>
            
            <div class="p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
                <div class="flex items-center space-x-2">
                    <i class="fas fa-info-circle text-yellow-600"></i>
                    <span class="text-sm text-yellow-800">
                        Webhook o'rnatish uchun avval bot token va webhook URL ni to'ldiring
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function testBot() {
    fetch(`/admin/bots/{{ $restaurant->id }}/test`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('✅ ' + data.message);
        } else {
            alert('❌ ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Xatolik yuz berdi');
    });
}

function sendTestMessage() {
    const chatId = document.getElementById('test_chat_id').value;
    const message = document.getElementById('test_message').value;
    
    if (!chatId || !message) {
        alert('Chat ID va xabar to\'ldiring');
        return;
    }
    
    fetch(`/admin/bots/{{ $restaurant->id }}/send-test`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            chat_id: chatId,
            message: message
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('✅ ' + data.message);
        } else {
            alert('❌ ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Xatolik yuz berdi');
    });
}

function setWebhook() {
    const webhookUrl = document.getElementById('webhook_url').value;
    
    if (!webhookUrl) {
        alert('Webhook URL ni to\'ldiring');
        return;
    }
    
    fetch(`/admin/bots/{{ $restaurant->id }}/webhook`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            webhook_url: webhookUrl
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('✅ ' + data.message);
        } else {
            alert('❌ ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Xatolik yuz berdi');
    });
}

function deleteWebhook() {
    if (!confirm('Webhook ni o\'chirmoqchimisiz?')) {
        return;
    }
    
    fetch(`/admin/bots/{{ $restaurant->id }}/webhook`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('✅ ' + data.message);
        } else {
            alert('❌ ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Xatolik yuz berdi');
    });
}
</script>
@endsection 