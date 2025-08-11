@extends('admin.layouts.app')

@section('title', 'Barcha Telegram Foydalanuvchilar')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Barcha Telegram Foydalanuvchilar</h1>
            <p class="text-gray-600">Barcha restoranlardagi foydalanuvchilarni ko'rish va boshqarish</p>
        </div>
        <div class="flex items-center space-x-3">
            <button onclick="loadGlobalStats()" 
                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                <i class="fas fa-chart-bar mr-2"></i>Statistika
            </button>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-users text-blue-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-500">Jami foydalanuvchilar</p>
                    <p class="text-2xl font-bold text-gray-800" id="total-users">{{ $users->total() }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-check text-green-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-500">Faol foydalanuvchilar</p>
                    <p class="text-2xl font-bold text-gray-800" id="active-users">-</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-comments text-purple-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-500">Jami xabarlar</p>
                    <p class="text-2xl font-bold text-gray-800" id="total-messages">-</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-store text-orange-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-500">Restoranlar</p>
                    <p class="text-2xl font-bold text-gray-800" id="total-restaurants">-</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Search and Filters -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
        <form method="GET" class="flex flex-wrap gap-4">
            <div class="flex-1 min-w-64">
                <input type="text" name="search" value="{{ request('search') }}" 
                       placeholder="Username, ism yoki telefon raqam bo'yicha qidirish..."
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
            <div>
                <select name="activity" class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">Barcha foydalanuvchilar</option>
                    <option value="active" {{ request('activity') === 'active' ? 'selected' : '' }}>Faol (7 kun)</option>
                    <option value="inactive" {{ request('activity') === 'inactive' ? 'selected' : '' }}>Faol emas (30 kun)</option>
                </select>
            </div>
            <div>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    <i class="fas fa-search mr-2"></i>Qidirish
                </button>
            </div>
        </form>
    </div>

    <!-- Users Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Foydalanuvchi
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Restoranlar
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Xabarlar
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
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                                    <i class="fas fa-user text-blue-600"></i>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">
                                        @if($user->first_name)
                                            {{ $user->first_name }}
                                            @if($user->last_name) {{ $user->last_name }}@endif
                                        @else
                                            {{ $user->username ?? 'Noma\'lum' }}
                                        @endif
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        @if($user->username)
                                            @{{ $user->username }}
                                        @endif
                                        @if($user->phone_number)
                                            <br>{{ $user->phone_number }}
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $user->restaurant_users_count }}</div>
                            <div class="text-sm text-gray-500">
                                @foreach($user->restaurantUsers->take(3) as $restaurantUser)
                                    <span class="inline-block bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded-full mb-1">
                                        {{ $restaurantUser->restaurant->name }}
                                    </span>
                                @endforeach
                                @if($user->restaurant_users_count > 3)
                                    <span class="text-xs text-gray-500">+{{ $user->restaurant_users_count - 3 }} ta</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $user->all_messages_count }}</div>
                            <div class="text-sm text-gray-500">
                                <span class="text-green-600">{{ $user->allMessages()->where('direction', 'incoming')->count() }} kiruvchi</span>
                                <br>
                                <span class="text-blue-600">{{ $user->allMessages()->where('direction', 'outgoing')->count() }} chiquvchi</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">
                                @if($user->last_activity)
                                    {{ $user->last_activity->diffForHumans() }}
                                @else
                                    <span class="text-gray-400">Ma'lum emas</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <a href="{{ route('admin.global-telegram.show', $user) }}" 
                               class="text-blue-600 hover:text-blue-900 mr-3">
                                <i class="fas fa-eye mr-1"></i>Ko'rish
                            </a>
                            <button onclick="sendMessageToUser({{ $user->id }})" 
                                    class="text-green-600 hover:text-green-900">
                                <i class="fas fa-paper-plane mr-1"></i>Xabar
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
            {{ $users->links() }}
        </div>
    </div>
</div>

<!-- Send Message Modal -->
<div id="sendMessageModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Foydalanuvchiga xabar yuborish</h3>
            <form id="sendMessageForm">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Xabar matni</label>
                    <textarea name="message" rows="4" required
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                              placeholder="Xabar matnini kiriting..."></textarea>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Restoranlar (ixtiyoriy)</label>
                    <div id="restaurantCheckboxes" class="space-y-2 max-h-32 overflow-y-auto">
                        <!-- Restaurant checkboxes will be loaded here -->
                    </div>
                </div>
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeSendMessageModal()" 
                            class="px-4 py-2 text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300 transition-colors">
                        Bekor
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-paper-plane mr-2"></i>Yuborish
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
let currentUserId = null;

function loadGlobalStats() {
    fetch('{{ route("admin.global-telegram.stats") }}')
        .then(response => response.json())
        .then(data => {
            document.getElementById('total-users').textContent = data.total_global_users;
            document.getElementById('active-users').textContent = data.active_users;
            document.getElementById('total-messages').textContent = data.total_messages;
            document.getElementById('total-restaurants').textContent = data.restaurants_with_bots;
        })
        .catch(error => {
            console.error('Error loading stats:', error);
        });
}

function sendMessageToUser(userId) {
    currentUserId = userId;
    
    // Load user's restaurants
    fetch(`/admin/global-telegram/${userId}/stats`)
        .then(response => response.json())
        .then(data => {
            const container = document.getElementById('restaurantCheckboxes');
            container.innerHTML = '';
            
            data.restaurant_activity.forEach(activity => {
                const div = document.createElement('div');
                div.className = 'flex items-center';
                div.innerHTML = `
                    <input type="checkbox" name="restaurant_ids[]" value="${activity.restaurant.id}" 
                           id="rest_${activity.restaurant.id}" class="mr-2" checked>
                    <label for="rest_${activity.restaurant.id}" class="text-sm text-gray-700">
                        ${activity.restaurant.name} (${activity.messages_count} xabar)
                    </label>
                `;
                container.appendChild(div);
            });
            
            document.getElementById('sendMessageModal').classList.remove('hidden');
        })
        .catch(error => {
            console.error('Error loading user stats:', error);
            alert('Foydalanuvchi ma\'lumotlarini yuklashda xatolik');
        });
}

function closeSendMessageModal() {
    document.getElementById('sendMessageModal').classList.add('hidden');
    document.getElementById('sendMessageForm').reset();
}

document.getElementById('sendMessageForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const restaurantIds = Array.from(formData.getAll('restaurant_ids[]'));
    
    fetch(`/admin/global-telegram/${currentUserId}/send-message`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            message: formData.get('message'),
            restaurant_ids: restaurantIds
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            closeSendMessageModal();
        } else {
            alert('Xatolik: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error sending message:', error);
        alert('Xabar yuborishda xatolik');
    });
});

// Load stats on page load
document.addEventListener('DOMContentLoaded', function() {
    loadGlobalStats();
});
</script>
@endpush 