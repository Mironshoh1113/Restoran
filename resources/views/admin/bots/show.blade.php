@extends('admin.layouts.app')

@section('title', 'Bot sozlamalari')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-200">{{ $restaurant->name }} - Bot sozlamalari</h1>
            <p class="text-gray-600 dark:text-gray-400">Telegram bot sozlamalarini boshqarish</p>
        </div>
        <div class="flex items-center space-x-3">
            <a href="{{ route('admin.bots.users', $restaurant) }}" 
               class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg flex items-center space-x-2 transition-colors">
                <i class="fas fa-users"></i>
                <span>Foydalanuvchilar</span>
            </a>
            <a href="{{ route('admin.bots.index') }}" 
               class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg flex items-center space-x-2 transition-colors">
                <i class="fas fa-arrow-left"></i>
                <span>Orqaga</span>
            </a>
        </div>
    </div>

    <!-- Bot Status Cards -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Bot Information Card -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center space-x-3 mb-4">
                <div class="w-12 h-12 bg-gradient-to-r from-blue-500 to-purple-600 rounded-xl flex items-center justify-center">
                    <i class="fas fa-robot text-white text-xl"></i>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Bot ma'lumotlari</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Asosiy ma'lumotlar</p>
                </div>
            </div>
            
            @if($botInfo && $botInfo['ok'])
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-gray-600 dark:text-gray-400">Bot nomi:</span>
                        <span class="font-medium text-gray-800 dark:text-gray-200">{{ $botInfo['result']['first_name'] }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-gray-600 dark:text-gray-400">Username:</span>
                        <span class="font-medium text-gray-800 dark:text-gray-200">@{{ $botInfo['result']['username'] }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-gray-600 dark:text-gray-400">Bot ID:</span>
                        <span class="font-medium text-gray-800 dark:text-gray-200">{{ $botInfo['result']['id'] }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-gray-600 dark:text-gray-400">Holat:</span>
                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 dark:bg-green-900/20 text-green-800 dark:text-green-200">
                            <i class="fas fa-check mr-1"></i>Faol
                        </span>
                    </div>
                </div>
            @else
                <div class="text-center py-4">
                    <i class="fas fa-exclamation-triangle text-yellow-500 dark:text-yellow-400 text-2xl mb-2"></i>
                    <p class="text-gray-600 dark:text-gray-400">Bot ma'lumotlari topilmadi</p>
                    <p class="text-xs text-gray-500 dark:text-gray-500 mt-1">Bot token ni kiriting va "Test qilish" tugmasini bosing</p>
                </div>
            @endif
        </div>

        <!-- Webhook Status Card -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center space-x-3 mb-4">
                <div class="w-12 h-12 bg-gradient-to-r from-green-500 to-teal-600 rounded-xl flex items-center justify-center">
                    <i class="fas fa-link text-white text-xl"></i>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Webhook holati</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Ulanish ma'lumotlari</p>
                </div>
            </div>
            
            @if($webhookInfo && $webhookInfo['ok'])
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-gray-600 dark:text-gray-400">Holat:</span>
                        <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $webhookInfo['result']['url'] ? 'bg-green-100 dark:bg-green-900/20 text-green-800 dark:text-green-200' : 'bg-red-100 dark:bg-red-900/20 text-red-800 dark:text-red-200' }}">
                            {{ $webhookInfo['result']['url'] ? 'Faol' : 'Faol emas' }}
                        </span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-gray-600 dark:text-gray-400">URL:</span>
                        <span class="font-medium text-xs text-gray-800 dark:text-gray-200 truncate max-w-32">{{ $webhookInfo['result']['url'] ?? 'O\'rnatilmagan' }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-gray-600 dark:text-gray-400">Xatolar:</span>
                        <span class="font-medium text-xs text-gray-800 dark:text-gray-200">{{ $webhookInfo['result']['last_error_message'] ?? 'Yo\'q' }}</span>
                    </div>
                </div>
            @else
                <div class="text-center py-4">
                    <i class="fas fa-exclamation-triangle text-yellow-500 dark:text-yellow-400 text-2xl mb-2"></i>
                    <p class="text-gray-600 dark:text-gray-400">Webhook ma'lumotlari topilmadi</p>
                    <p class="text-xs text-gray-500 dark:text-gray-500 mt-1">Webhook URL ni kiriting va "Webhook o'rnatish" tugmasini bosing</p>
                </div>
            @endif
        </div>

        <!-- Quick Actions Card -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center space-x-3 mb-4">
                <div class="w-12 h-12 bg-gradient-to-r from-orange-500 to-red-600 rounded-xl flex items-center justify-center">
                    <i class="fas fa-cogs text-white text-xl"></i>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Tezkor amallar</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Bot boshqaruvi</p>
                </div>
            </div>
            
            <div class="space-y-2">
                <button onclick="testBot()" 
                        class="w-full px-3 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm rounded-lg transition-colors flex items-center justify-center space-x-2">
                    <i class="fas fa-play"></i>
                    <span>Test qilish</span>
                </button>
                <button onclick="setWebhook()" 
                        class="w-full px-3 py-2 bg-green-600 hover:bg-green-700 text-white text-sm rounded-lg transition-colors flex items-center justify-center space-x-2">
                    <i class="fas fa-link"></i>
                    <span>Webhook o'rnatish</span>
                </button>
                <button onclick="deleteWebhook()" 
                        class="w-full px-3 py-2 bg-red-600 hover:bg-red-700 text-white text-sm rounded-lg transition-colors flex items-center justify-center space-x-2">
                    <i class="fas fa-unlink"></i>
                    <span>Webhook o'chirish</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Bot Profile Management -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Bot Image Upload -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center space-x-3 mb-4">
                <div class="w-12 h-12 bg-gradient-to-r from-purple-500 to-pink-600 rounded-xl flex items-center justify-center">
                    <i class="fas fa-image text-white text-xl"></i>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Bot rasmi</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Profil rasmini yangilash</p>
                </div>
            </div>
            
            <div class="space-y-4">
                @if($restaurant->bot_image)
                    <div class="flex items-center space-x-4">
                        <img src="{{ Storage::url($restaurant->bot_image) }}" alt="Bot Image" class="w-16 h-16 rounded-lg object-cover">
                        <div>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Joriy rasm</p>
                            <p class="text-xs text-gray-500 dark:text-gray-500">Telegram da ko'rinadi</p>
                        </div>
                    </div>
                @endif
                
                <form id="botPhotoForm" enctype="multipart/form-data" class="space-y-3">
                    <div>
                        <label for="bot_photo" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Yangi rasm</label>
                        <input type="file" id="bot_photo" name="bot_photo" accept="image/*"
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">JPG, PNG formatida, maksimal 1MB</p>
                    </div>
                    <button type="button" onclick="updateBotPhoto()" 
                            class="w-full px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg transition-colors flex items-center justify-center space-x-2">
                        <i class="fas fa-upload"></i>
                        <span>Rasmni yangilash</span>
                    </button>
                </form>
            </div>
        </div>

        <!-- Bot Name & Description -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center space-x-3 mb-4">
                <div class="w-12 h-12 bg-gradient-to-r from-indigo-500 to-blue-600 rounded-xl flex items-center justify-center">
                    <i class="fas fa-edit text-white text-xl"></i>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Bot ma'lumotlari</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Nomi va tavsifi</p>
                </div>
            </div>
            
            <div class="space-y-4">
                <div>
                    <label for="edit_bot_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Bot nomi</label>
                    <input type="text" id="edit_bot_name" value="{{ $restaurant->bot_name ?? '' }}"
                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                           placeholder="Restaurant Bot">
                    <button onclick="updateBotName()" 
                            class="mt-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors flex items-center space-x-2">
                        <i class="fas fa-save"></i>
                        <span>Nomi yangilash</span>
                    </button>
                </div>
                
                <div>
                    <label for="edit_bot_description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Bot tavsifi</label>
                    <textarea id="edit_bot_description" rows="3"
                              class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                              placeholder="Bot haqida qisqacha ma'lumot...">{{ $restaurant->bot_description ?? '' }}</textarea>
                    <button onclick="updateBotDescription()" 
                            class="mt-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors flex items-center space-x-2">
                        <i class="fas fa-save"></i>
                        <span>Tavsif yangilash</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bot Commands Management -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
            <div class="flex items-center space-x-3">
                <div class="w-12 h-12 bg-gradient-to-r from-yellow-500 to-orange-600 rounded-xl flex items-center justify-center">
                    <i class="fas fa-terminal text-white text-xl"></i>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Bot buyruqlari</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Telegram bot buyruqlarini boshqarish</p>
                </div>
            </div>
        </div>
        
        <div class="p-6 space-y-4">
            <div class="flex items-center justify-between">
                <button onclick="getBotCommands()" 
                        class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition-colors flex items-center space-x-2">
                    <i class="fas fa-download"></i>
                    <span>Buyruqlarni yuklash</span>
                </button>
                <button onclick="showCommandsEditor()" 
                        class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors flex items-center space-x-2">
                    <i class="fas fa-edit"></i>
                    <span>Buyruqlarni tahrirlash</span>
                </button>
            </div>
            
            <div id="commandsList" class="hidden">
                <h4 class="text-md font-semibold text-gray-800 dark:text-gray-200 mb-3">Joriy buyruqlar:</h4>
                <div id="commandsContainer" class="space-y-2"></div>
            </div>
            
            <div id="commandsEditor" class="hidden">
                <h4 class="text-md font-semibold text-gray-800 dark:text-gray-200 mb-3">Buyruqlarni tahrirlash:</h4>
                <div id="commandsForm" class="space-y-3"></div>
                <button onclick="saveBotCommands()" 
                        class="mt-4 px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition-colors flex items-center space-x-2">
                    <i class="fas fa-save"></i>
                    <span>Buyruqlarni saqlash</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Setup Instructions -->
    <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-xl p-6">
        <div class="flex items-start space-x-3">
            <i class="fas fa-info-circle text-blue-600 dark:text-blue-400 text-xl mt-1"></i>
            <div>
                <h3 class="text-lg font-semibold text-blue-800 dark:text-blue-200 mb-2">Telegram Bot Sozlash</h3>
                <div class="text-blue-700 dark:text-blue-300 space-y-2 text-sm">
                    <p><strong>1.</strong> Telegram da @BotFather ga murojaat qiling</p>
                    <p><strong>2.</strong> <code>/newbot</code> buyrug'ini yuboring</p>
                    <p><strong>3.</strong> Bot nomi va username ni kiriting</p>
                    <p><strong>4.</strong> Bot token ni nusxalab saqlang</p>
                    <p><strong>5.</strong> Quyidagi forma orqali bot token va webhook URL ni kiriting</p>
                    <p><strong>6.</strong> "Saqlash" tugmasini bosing</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Bot Settings Form -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Bot sozlamalari</h3>
        </div>
        
        <form action="{{ route('admin.bots.update', $restaurant) }}" method="POST" class="p-6 space-y-6" enctype="multipart/form-data">
            @csrf
            @method('PATCH')
            
            <!-- Bot Basic Settings -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="bot_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Bot nomi</label>
                    <input type="text" id="bot_name" name="bot_name" value="{{ old('bot_name', $restaurant->bot_name ?? '') }}"
                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                           placeholder="Restaurant Bot">
                    @error('bot_name')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Botning ko'rinadigan nomi</p>
                </div>
                
                <div>
                    <label for="bot_username" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Bot username</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500 dark:text-gray-400">@</span>
                        <input type="text" id="bot_username" name="bot_username" value="{{ old('bot_username', $restaurant->bot_username) }}"
                               class="w-full pl-8 pr-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                               placeholder="restaurant_bot">
                    </div>
                    @error('bot_username')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">BotFather dan olingan username</p>
                </div>
            </div>
            
            <!-- Bot Token -->
            <div>
                <label for="bot_token" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Bot token</label>
                <div class="relative">
                    <input type="password" id="bot_token" name="bot_token" value="{{ old('bot_token', $restaurant->bot_token) }}"
                           class="w-full pr-10 px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                           placeholder="1234567890:ABCdefGHIjklMNOpqrsTUVwxyz">
                    <button type="button" onclick="togglePassword('bot_token')" 
                            class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300">
                        <i class="fas fa-eye" id="bot_token_icon"></i>
                    </button>
                </div>
                @error('bot_token')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">BotFather dan olingan token (xavfsizlik uchun yashirin)</p>
            </div>
            
            <!-- Webhook URL -->
            <div>
                <label for="webhook_url" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Webhook URL</label>
                <input type="url" id="webhook_url" name="webhook_url" value="{{ old('webhook_url', url('/telegram/webhook/' . ($restaurant->bot_token ? hash('sha256', $restaurant->bot_token) : ''))) }}"
                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                       placeholder="https://example.com/telegram/webhook/token">
                @error('webhook_url')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Telegram webhook URL manzili (avtomatik yaratiladi)</p>
            </div>
            
            <!-- Bot Description -->
            <div>
                <label for="bot_description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Bot tavsifi</label>
                <textarea id="bot_description" name="bot_description" rows="3"
                          class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                          placeholder="Bot haqida qisqacha ma'lumot...">{{ old('bot_description', $restaurant->bot_description ?? '') }}</textarea>
                @error('bot_description')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Bot haqida ma'lumot (ixtiyoriy)</p>
            </div>
            
            <!-- Save Button -->
            <div class="flex items-center justify-end space-x-3 pt-4">
                <button type="button" onclick="testBot()" 
                        class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition-colors flex items-center space-x-2">
                    <i class="fas fa-play"></i>
                    <span>Test qilish</span>
                </button>
                <button type="submit" 
                        class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors flex items-center space-x-2">
                    <i class="fas fa-save"></i>
                    <span>Saqlash</span>
                </button>
            </div>
        </form>
    </div>

    <!-- Test Message Section -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Test xabar yuborish</h3>
        </div>
        
        <div class="p-6 space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="test_chat_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Chat ID</label>
                    <input type="number" id="test_chat_id" 
                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                           placeholder="123456789">
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Telegram chat ID raqami</p>
                </div>
                <div>
                    <label for="test_message" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Xabar</label>
                    <input type="text" id="test_message" value="Test xabar"
                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                           placeholder="Test xabar">
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Yuboriladigan xabar</p>
                </div>
            </div>
            
            <button onclick="sendTestMessage()" 
                    class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg transition-colors flex items-center space-x-2">
                <i class="fas fa-paper-plane"></i>
                <span>Xabar yuborish</span>
            </button>
        </div>
    </div>
</div>

<script>
function togglePassword(fieldId) {
    const field = document.getElementById(fieldId);
    const icon = document.getElementById(fieldId + '_icon');
    
    if (field.type === 'password') {
        field.type = 'text';
        icon.className = 'fas fa-eye-slash';
    } else {
        field.type = 'password';
        icon.className = 'fas fa-eye';
    }
}

function updateBotPhoto() {
    const formData = new FormData();
    const fileInput = document.getElementById('bot_photo');
    
    if (!fileInput.files[0]) {
        showNotification('Rasm tanlang', 'warning');
        return;
    }
    
    formData.append('bot_photo', fileInput.files[0]);
    formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
    
    fetch(`/admin/bots/{{ $restaurant->id }}/update-photo`, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('✅ ' + data.message, 'success');
            setTimeout(() => location.reload(), 1500);
        } else {
            showNotification('❌ ' + data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Xatolik yuz berdi', 'error');
    });
}

function updateBotName() {
    const botName = document.getElementById('edit_bot_name').value;
    
    if (!botName) {
        showNotification('Bot nomini kiriting', 'warning');
        return;
    }
    
    fetch(`/admin/bots/{{ $restaurant->id }}/update-name`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            bot_name: botName
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

function updateBotDescription() {
    const botDescription = document.getElementById('edit_bot_description').value;
    
    if (!botDescription) {
        showNotification('Bot tavsifini kiriting', 'warning');
        return;
    }
    
    fetch(`/admin/bots/{{ $restaurant->id }}/update-description`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            bot_description: botDescription
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

function getBotCommands() {
    fetch(`/admin/bots/{{ $restaurant->id }}/commands`, {
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            displayCommands(data.commands);
            showNotification('✅ Buyruqlar yuklandi', 'success');
        } else {
            showNotification('❌ ' + data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Xatolik yuz berdi', 'error');
    });
}

function displayCommands(commands) {
    const container = document.getElementById('commandsContainer');
    const list = document.getElementById('commandsList');
    
    container.innerHTML = '';
    
    commands.forEach(command => {
        const commandDiv = document.createElement('div');
        commandDiv.className = 'flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg';
        commandDiv.innerHTML = `
            <div>
                <span class="font-medium text-gray-800 dark:text-gray-200">/${command.command}</span>
                <p class="text-sm text-gray-600 dark:text-gray-400">${command.description}</p>
            </div>
        `;
        container.appendChild(commandDiv);
    });
    
    list.classList.remove('hidden');
}

function showCommandsEditor() {
    const editor = document.getElementById('commandsEditor');
    const form = document.getElementById('commandsForm');
    
    form.innerHTML = `
        <div class="space-y-3">
            <div class="flex items-center justify-between">
                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Buyruq</span>
                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Tavsif</span>
            </div>
            <div id="commandsList">
                <div class="flex items-center space-x-3">
                    <input type="text" name="commands[0][command]" placeholder="start" 
                           class="flex-1 px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                    <input type="text" name="commands[0][description]" placeholder="Botni ishga tushirish" 
                           class="flex-1 px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                </div>
                <div class="flex items-center space-x-3">
                    <input type="text" name="commands[1][command]" placeholder="menu" 
                           class="flex-1 px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                    <input type="text" name="commands[1][description]" placeholder="Menyu ko'rish" 
                           class="flex-1 px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                </div>
                <div class="flex items-center space-x-3">
                    <input type="text" name="commands[2][command]" placeholder="help" 
                           class="flex-1 px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                    <input type="text" name="commands[2][description]" placeholder="Yordam" 
                           class="flex-1 px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                </div>
            </div>
        </div>
    `;
    
    editor.classList.remove('hidden');
}

function saveBotCommands() {
    const form = document.getElementById('commandsForm');
    const inputs = form.querySelectorAll('input[name*="[command]"]');
    const commands = [];
    
    inputs.forEach((input, index) => {
        const command = input.value.trim();
        const description = form.querySelector(`input[name="commands[${index}][description]"]`).value.trim();
        
        if (command && description) {
            commands.push({
                command: command,
                description: description
            });
        }
    });
    
    if (commands.length === 0) {
        showNotification('Kamida bitta buyruq kiriting', 'warning');
        return;
    }
    
    fetch(`/admin/bots/{{ $restaurant->id }}/commands`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            commands: commands
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('✅ ' + data.message, 'success');
            document.getElementById('commandsEditor').classList.add('hidden');
        } else {
            showNotification('❌ ' + data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Xatolik yuz berdi', 'error');
    });
}

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

function sendTestMessage() {
    const chatId = document.getElementById('test_chat_id').value;
    const message = document.getElementById('test_message').value;
    
    if (!chatId || !message) {
        showNotification('Chat ID va xabar to\'ldiring', 'warning');
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

function setWebhook() {
    const webhookUrl = document.getElementById('webhook_url').value;
    
    if (!webhookUrl) {
        showNotification('Webhook URL ni to\'ldiring', 'warning');
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

function showNotification(message, type = 'info') {
    const colors = {
        success: 'bg-green-500',
        error: 'bg-red-500',
        warning: 'bg-yellow-500',
        info: 'bg-blue-500'
    };
    
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 ${colors[type]} text-white px-6 py-3 rounded-lg shadow-lg z-50 transform transition-all duration-300 translate-x-full`;
    notification.textContent = message;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.classList.remove('translate-x-full');
    }, 100);
    
    setTimeout(() => {
        notification.classList.add('translate-x-full');
        setTimeout(() => {
            document.body.removeChild(notification);
        }, 300);
    }, 3000);
}
</script>
@endsection 