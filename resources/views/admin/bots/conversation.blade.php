@extends('admin.layouts.app')

@section('title', 'Xabar almashinuvi')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-200">
                {{ $telegramUser->display_name }} bilan xabar almashinuvi
            </h1>
            <p class="text-gray-600 dark:text-gray-400">
                {{ $restaurant->name }} - Telegram bot
            </p>
        </div>
        <div class="flex items-center space-x-3">
            <button onclick="markAsRead()" 
                    class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg flex items-center space-x-2 transition-colors">
                <i class="fas fa-check"></i>
                <span>O'qildi deb belgilash</span>
            </button>
            <a href="{{ route('admin.bots.users', $restaurant) }}" 
               class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg flex items-center space-x-2 transition-colors">
                <i class="fas fa-arrow-left"></i>
                <span>Orqaga</span>
            </a>
        </div>
    </div>

    <!-- User Info -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
        <div class="flex items-center space-x-4">
            <div class="w-16 h-16 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center">
                <span class="text-white font-semibold text-xl">{{ substr($telegramUser->first_name ?? 'U', 0, 1) }}</span>
            </div>
            <div class="flex-1">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">{{ $telegramUser->full_name }}</h3>
                <p class="text-gray-600 dark:text-gray-400">{{ $telegramUser->display_name }}</p>
                <p class="text-sm text-gray-500 dark:text-gray-500">ID: {{ $telegramUser->telegram_id }}</p>
                <p class="text-sm text-gray-500 dark:text-gray-500">
                    So'nggi faollik: {{ $telegramUser->last_activity ? $telegramUser->last_activity->diffForHumans() : 'Noma\'lum' }}
                </p>
            </div>
            <div class="text-right">
                <div class="text-sm text-gray-500 dark:text-gray-400">
                    <span class="font-medium">{{ $telegramUser->messages()->count() }}</span> xabar
                </div>
                @if($telegramUser->unread_messages_count > 0)
                    <div class="mt-1">
                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 dark:bg-red-900/20 text-red-800 dark:text-red-200">
                            {{ $telegramUser->unread_messages_count }} o'qilmagan
                        </span>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Messages Container -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Xabarlar</h3>
        </div>
        
        <div class="p-6">
            <div id="messagesContainer" class="space-y-4 max-h-96 overflow-y-auto">
                @if($messages->count() > 0)
                    @foreach($messages as $message)
                        <div class="flex {{ $message->direction === 'incoming' ? 'justify-start' : 'justify-end' }}">
                            <div class="max-w-xs lg:max-w-md px-4 py-2 rounded-lg {{ $message->direction === 'incoming' ? 'bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200' : 'bg-blue-600 text-white' }}">
                                <div class="text-sm">{{ $message->message_text }}</div>
                                <div class="text-xs mt-1 {{ $message->direction === 'incoming' ? 'text-gray-500 dark:text-gray-400' : 'text-blue-100' }}">
                                    {{ $message->created_at->format('H:i') }}
                                    @if($message->direction === 'incoming' && !$message->is_read)
                                        <span class="ml-2">●</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="text-center py-8">
                        <div class="w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-comments text-gray-400 text-xl"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-600 dark:text-gray-400 mb-2">Xabarlar yo'q</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-500">Foydalanuvchi bilan hali xabar almashinmagan</p>
                    </div>
                @endif
            </div>
            
            <!-- Message Input -->
            <div class="mt-6 pt-4 border-t border-gray-200 dark:border-gray-700">
                <div class="flex space-x-3">
                    <input type="text" id="messageInput" 
                           class="flex-1 px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                           placeholder="Xabar yozing..." 
                           onkeypress="if(event.key === 'Enter') sendMessage()">
                    <button id="sendButton" onclick="sendMessage()" 
                            class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors flex items-center space-x-2 disabled:opacity-50 disabled:cursor-not-allowed">
                        <span id="sendButtonText">Yuborish</span>
                        <div id="sendButtonSpinner" class="hidden">
                            <i class="fas fa-spinner fa-spin"></i>
                        </div>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let lastMessageTime = '{{ $messages->last() ? $messages->last()->created_at->toISOString() : now()->toISOString() }}';
let isPolling = false;

// Show notification
function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 px-6 py-3 rounded-lg text-white z-50 transform transition-all duration-300 translate-x-full`;
    
    switch (type) {
        case 'success':
            notification.classList.add('bg-green-600');
            break;
        case 'error':
            notification.classList.add('bg-red-600');
            break;
        case 'warning':
            notification.classList.add('bg-yellow-600');
            break;
        case 'info':
            notification.classList.add('bg-blue-600');
            break;
        default:
            notification.classList.add('bg-blue-600');
    }
    
    notification.textContent = message;
    document.body.appendChild(notification);
    
    // Animate in
    setTimeout(() => {
        notification.classList.remove('translate-x-full');
    }, 100);
    
    // Remove after 5 seconds
    setTimeout(() => {
        notification.classList.add('translate-x-full');
        setTimeout(() => {
            if (document.body.contains(notification)) {
                document.body.removeChild(notification);
            }
        }, 300);
    }, 5000);
}

// Auto-scroll to bottom
function scrollToBottom() {
    const container = document.getElementById('messagesContainer');
    container.scrollTop = container.scrollHeight;
}

// Send message
function sendMessage() {
    const input = document.getElementById('messageInput');
    const message = input.value.trim();
    
    if (!message) {
        showNotification('Xabar matnini kiriting', 'warning');
        return;
    }
    
    fetch(`/admin/bots/{{ $restaurant->id }}/users/{{ $telegramUser->id }}/send`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            message: message
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Add message to container
            addMessageToContainer(data.message_data);
            input.value = '';
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

// Add message to container
function addMessageToContainer(messageData) {
    const container = document.getElementById('messagesContainer');
    
    // Remove empty state if it exists
    const emptyState = container.querySelector('.text-center');
    if (emptyState) {
        emptyState.remove();
    }
    
    const messageDiv = document.createElement('div');
    messageDiv.className = 'flex justify-end';
    
    const time = new Date(messageData.created_at).toLocaleTimeString('uz-UZ', { hour: '2-digit', minute: '2-digit' });
    
    messageDiv.innerHTML = `
        <div class="max-w-xs lg:max-w-md px-4 py-2 rounded-lg bg-blue-600 text-white">
            <div class="text-sm">${messageData.text}</div>
            <div class="text-xs mt-1 text-blue-100">${time}</div>
        </div>
    `;
    
    container.appendChild(messageDiv);
    scrollToBottom();
}

// Poll for new messages
function pollNewMessages() {
    if (isPolling) return;
    
    isPolling = true;
    
    fetch(`/admin/bots/{{ $restaurant->id }}/users/{{ $telegramUser->id }}/messages/new?last_message_time=${lastMessageTime}`)
        .then(response => response.json())
        .then(data => {
            if (data.success && data.messages.length > 0) {
                data.messages.forEach(message => {
                    if (message.direction === 'incoming') {
                        addIncomingMessageToContainer(message);
                    }
                });
                
                // Update last message time
                const lastMessage = data.messages[data.messages.length - 1];
                lastMessageTime = new Date(lastMessage.created_at).toISOString();
                
                // Show notification for new messages
                if (data.messages.length > 0) {
                    showNotification(`📨 ${data.messages.length} ta yangi xabar`, 'info');
                }
            }
        })
        .catch(error => {
            console.error('Error polling messages:', error);
        })
        .finally(() => {
            isPolling = false;
        });
}

// Add incoming message to container
function addIncomingMessageToContainer(message) {
    const container = document.getElementById('messagesContainer');
    
    // Remove empty state if it exists
    const emptyState = container.querySelector('.text-center');
    if (emptyState) {
        emptyState.remove();
    }
    
    const messageDiv = document.createElement('div');
    messageDiv.className = 'flex justify-start';
    
    const time = new Date(message.created_at).toLocaleTimeString('uz-UZ', { hour: '2-digit', minute: '2-digit' });
    const unreadIndicator = !message.is_read ? '<span class="ml-2">●</span>' : '';
    
    messageDiv.innerHTML = `
        <div class="max-w-xs lg:max-w-md px-4 py-2 rounded-lg bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200">
            <div class="text-sm">${message.message_text}</div>
            <div class="text-xs mt-1 text-gray-500 dark:text-gray-400">
                ${time}${unreadIndicator}
            </div>
        </div>
    `;
    
    container.appendChild(messageDiv);
    scrollToBottom();
}

// Mark messages as read
function markAsRead() {
    fetch(`/admin/bots/{{ $restaurant->id }}/users/{{ $telegramUser->id }}/read`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('✅ ' + data.message, 'success');
            // Remove unread indicators
            document.querySelectorAll('.text-gray-500 .ml-2').forEach(el => el.remove());
        } else {
            showNotification('❌ ' + data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Xatolik yuz berdi', 'error');
    });
}

// Send message function
function sendMessage() {
    const messageInput = document.getElementById('messageInput');
    const sendButton = document.getElementById('sendButton');
    const sendButtonText = document.getElementById('sendButtonText');
    const sendButtonSpinner = document.getElementById('sendButtonSpinner');
    
    const message = messageInput.value.trim();
    if (!message) return;
    
    // Disable button and show loading
    sendButton.disabled = true;
    sendButtonText.classList.add('hidden');
    sendButtonSpinner.classList.remove('hidden');
    
    fetch(`/admin/bots/{{ $restaurant->id }}/users/{{ $telegramUser->id }}/send`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ message: message })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Add message to container
            addOutgoingMessageToContainer(message);
            messageInput.value = '';
            showNotification('✅ ' + data.message, 'success');
        } else {
            showNotification('❌ ' + data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Xabar yuborishda xatolik', 'error');
    })
    .finally(() => {
        // Re-enable button
        sendButton.disabled = false;
        sendButtonText.classList.remove('hidden');
        sendButtonSpinner.classList.add('hidden');
    });
}

// Add outgoing message to container
function addOutgoingMessageToContainer(messageText) {
    const container = document.getElementById('messagesContainer');
    
    // Remove empty state if it exists
    const emptyState = container.querySelector('.text-center');
    if (emptyState) {
        emptyState.remove();
    }
    
    const messageDiv = document.createElement('div');
    messageDiv.className = 'flex justify-end';
    
    const time = new Date().toLocaleTimeString('uz-UZ', { hour: '2-digit', minute: '2-digit' });
    
    messageDiv.innerHTML = `
        <div class="max-w-xs lg:max-w-md px-4 py-2 rounded-lg bg-blue-600 text-white">
            <div class="text-sm">${messageText}</div>
            <div class="text-xs mt-1 text-blue-100">
                ${time}
            </div>
        </div>
    `;
    
    container.appendChild(messageDiv);
    scrollToBottom();
}

// Enter key to send message
document.getElementById('messageInput').addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        sendMessage();
    }
});

// Start polling for new messages
setInterval(pollNewMessages, 3000); // Poll every 3 seconds

// Initial scroll to bottom
document.addEventListener('DOMContentLoaded', function() {
    scrollToBottom();
});
</script>
@endsection 