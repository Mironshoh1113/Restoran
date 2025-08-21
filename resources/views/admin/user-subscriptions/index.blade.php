@extends('admin.layouts.app')

@section('content')
<div class="max-w-7xl mx-auto py-6">
    <h1 class="text-2xl font-semibold mb-6">Foydalanuvchilar va Obunalar</h1>
    
    @if(session('success'))
        <div class="p-4 bg-green-100 text-green-800 rounded mb-4">{{ session('success') }}</div>
    @endif
    
    @if(session('error'))
        <div class="p-4 bg-red-100 text-red-800 rounded mb-4">{{ session('error') }}</div>
    @endif

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Foydalanuvchi
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Joriy Tarif
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Holati
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Boshlanish
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Tugash
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Amallar
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($users as $user)
                    @php
                        $currentSubscription = $user->subscriptions
                            ->where('status', 'active')
                            ->where(function($q) { 
                                return $q->whereNull('ends_at') || $q->where('ends_at', '>=', now()); 
                            })
                            ->first();
                    @endphp
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10">
                                    <div class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center">
                                        <span class="text-sm font-medium text-gray-700">
                                            {{ strtoupper(substr($user->name, 0, 1)) }}
                                        </span>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $user->name }}</div>
                                    <div class="text-sm text-gray-500">{{ $user->email }}</div>
                                    <div class="text-xs text-gray-400">
                                        Ro'yxatdan o'tgan: {{ $user->created_at->format('Y-m-d') }}
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($currentSubscription)
                                <div class="text-sm text-gray-900">
                                    <span class="font-medium">{{ $currentSubscription->plan->name }}</span>
                                </div>
                                <div class="text-sm text-gray-500">
                                    {{ number_format($currentSubscription->plan->price, 2) }} so'm
                                </div>
                            @else
                                <span class="text-sm text-gray-400">Tarif yo'q</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($currentSubscription)
                                @if($currentSubscription->status === 'active')
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        Faol
                                    </span>
                                @else
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                        {{ ucfirst($currentSubscription->status) }}
                                    </span>
                                @endif
                            @else
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                    Obuna yo'q
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            @if($currentSubscription && $currentSubscription->starts_at)
                                {{ $currentSubscription->starts_at->format('Y-m-d') }}
                            @else
                                -
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            @if($currentSubscription && $currentSubscription->ends_at)
                                {{ $currentSubscription->ends_at->format('Y-m-d') }}
                            @else
                                -
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <a href="{{ route('admin.user-subscriptions.show', $user) }}" 
                               class="text-indigo-600 hover:text-indigo-900 mr-3">
                                Batafsil
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-6">
        {{ $users->links() }}
    </div>
</div>
@endsection 