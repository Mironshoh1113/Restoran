@extends('admin.layouts.app')

@section('content')
<div class="max-w-6xl mx-auto py-6">
    <div class="mb-6">
        <a href="{{ route('admin.user-subscriptions.index') }}" class="text-indigo-600 hover:text-indigo-900">
            ← Foydalanuvchilar ro'yxatiga qaytish
        </a>
    </div>

    <h1 class="text-2xl font-semibold mb-6">{{ $user->name }} - Obuna ma'lumotlari</h1>
    
    @if(session('success'))
        <div class="p-4 bg-green-100 text-green-800 rounded mb-4">{{ session('success') }}</div>
    @endif
    
    @if(session('error'))
        <div class="p-4 bg-red-100 text-red-800 rounded mb-4">{{ session('error') }}</div>
    @endif

    <!-- User Information -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <h2 class="text-lg font-medium mb-4">Foydalanuvchi ma'lumotlari</h2>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">Ism</label>
                <p class="mt-1 text-sm text-gray-900">{{ $user->name }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Email</label>
                <p class="mt-1 text-sm text-gray-900">{{ $user->email }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Ro'yxatdan o'tgan sana</label>
                <p class="mt-1 text-sm text-gray-900">{{ $user->created_at->format('Y-m-d H:i') }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Rol</label>
                <p class="mt-1 text-sm text-gray-900">{{ ucfirst($user->role ?? 'user') }}</p>
            </div>
        </div>
    </div>

    <!-- Current Subscription -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <h2 class="text-lg font-medium mb-4">Joriy obuna</h2>
        
        @if($currentSubscription)
            <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Tarif nomi</label>
                        <p class="mt-1 text-lg font-semibold text-green-800">{{ $currentSubscription->plan->name }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Narxi</label>
                        <p class="mt-1 text-lg font-semibold text-green-800">{{ number_format($currentSubscription->plan->price, 2) }} so'm</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Holati</label>
                        <span class="mt-1 px-3 py-1 inline-flex text-sm font-semibold rounded-full bg-green-100 text-green-800">
                            {{ ucfirst($currentSubscription->status) }}
                        </span>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Davomiyligi</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $currentSubscription->plan->duration_days }} kun</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Boshlanish</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $currentSubscription->starts_at->format('Y-m-d') }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Tugash</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $currentSubscription->ends_at->format('Y-m-d') }}</p>
                    </div>
                </div>
                
                <!-- Plan Limits -->
                @if($currentSubscription->plan->limits)
                    <div class="mt-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tarif cheklovlari</label>
                        <div class="grid grid-cols-2 gap-4">
                            @foreach($currentSubscription->plan->limits as $key => $value)
                                <div class="bg-white rounded p-2">
                                    <span class="text-sm font-medium text-gray-700">{{ ucfirst(str_replace('_', ' ', $key)) }}:</span>
                                    <span class="text-sm text-gray-900 ml-2">{{ $value }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        @else
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                <p class="text-yellow-800">Bu foydalanuvchida faol obuna yo'q</p>
            </div>
        @endif
    </div>

    <!-- Subscription History -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <h2 class="text-lg font-medium mb-4">Obuna tarixi</h2>
        
        @if($user->subscriptions->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tarif</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Holati</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Boshlanish</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tugash</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($user->subscriptions->sortByDesc('created_at') as $subscription)
                            <tr class="{{ $subscription->id === $currentSubscription?->id ? 'bg-green-50' : '' }}">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $subscription->plan->name }} ({{ number_format($subscription->plan->price, 2) }} so'm)
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($subscription->status === 'active')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            Faol
                                        </span>
                                    @elseif($subscription->status === 'expired')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                            Muddati tugagan
                                        </span>
                                    @else
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                            {{ ucfirst($subscription->status) }}
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $subscription->starts_at ? $subscription->starts_at->format('Y-m-d') : '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $subscription->ends_at ? $subscription->ends_at->format('Y-m-d') : '-' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p class="text-gray-500">Obuna tarixi yo'q</p>
        @endif
    </div>

    <!-- Actions -->
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-lg font-medium mb-4">Amallar</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Assign New Plan -->
            <div class="border border-gray-200 rounded-lg p-4">
                <h3 class="text-md font-medium mb-3">Yangi tarif biriktirish</h3>
                <form method="POST" action="{{ route('admin.user-subscriptions.assign-plan', $user) }}">
                    @csrf
                    <div class="space-y-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Tarif</label>
                            <select name="plan_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" required>
                                @foreach($plans as $plan)
                                    <option value="{{ $plan->id }}" {{ $currentSubscription && $currentSubscription->plan_id === $plan->id ? 'selected' : '' }}>
                                        {{ $plan->name }} - {{ number_format($plan->price, 2) }} so'm
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Boshlanish sanasi</label>
                            <input type="date" name="starts_at" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Tugash sanasi</label>
                            <input type="date" name="ends_at" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" />
                        </div>
                        <button type="submit" class="w-full bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">
                            Tarif biriktirish
                        </button>
                    </div>
                </form>
            </div>

            <!-- Other Actions -->
            <div class="border border-gray-200 rounded-lg p-4">
                <h3 class="text-md font-medium mb-3">Boshqa amallar</h3>
                <div class="space-y-3">
                    @if($currentSubscription)
                        <form method="POST" action="{{ route('admin.user-subscriptions.cancel-subscription', $user) }}" 
                              onsubmit="return confirm('Obunani bekor qilishni xohlaysizmi?')">
                            @csrf
                            <button type="submit" class="w-full bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700">
                                Obunani bekor qilish
                            </button>
                        </form>
                    @endif
                    
                    <form method="POST" action="{{ route('admin.user-subscriptions.reset-to-free-plan', $user) }}"
                          onsubmit="return confirm('Foydalanuvchini bepul tarifga qaytarishni xohlaysizmi?')">
                        @csrf
                        <button type="submit" class="w-full bg-yellow-600 text-white px-4 py-2 rounded-md hover:bg-yellow-700">
                            Bepul tarifga qaytarish
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 