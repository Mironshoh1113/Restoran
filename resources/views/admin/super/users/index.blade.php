@extends('admin.layouts.app')

@section('content')
<div class="max-w-7xl mx-auto py-6">
	<div class="flex items-center justify-between mb-4">
		<h1 class="text-xl font-semibold">Foydalanuvchilar va ularning tariflari</h1>
	</div>
	@if(session('success'))
		<div class="p-3 bg-green-100 text-green-800 rounded mb-3">{{ session('success') }}</div>
	@endif
	<table class="w-full bg-white rounded shadow">
		<thead>
			<tr class="text-left border-b">
				<th class="p-3">Foydalanuvchi</th>
				<th class="p-3">Email</th>
				<th class="p-3">Ro'l</th>
				<th class="p-3">Joriy tarif</th>
				<th class="p-3">Restoran</th>
				<th class="p-3">Obuna holati</th>
				<th class="p-3">Ro'yxatdan o'tgan</th>
				<th class="p-3"></th>
			</tr>
		</thead>
		<tbody>
			@foreach($users as $user)
			<tr class="border-b">
				<td class="p-3">
					<div class="flex items-center">
						<div class="w-8 h-8 bg-gradient-to-r from-orange-500 to-red-500 rounded-full flex items-center justify-center mr-2">
							<span class="text-white font-semibold text-sm">{{ substr($user->name, 0, 1) }}</span>
						</div>
						{{ $user->name }}
					</div>
				</td>
				<td class="p-3">{{ $user->email }}</td>
				<td class="p-3">
					<span class="px-2 py-1 rounded text-xs 
						{{ $user->role === 'super_admin' ? 'bg-purple-100 text-purple-800' : 
						   ($user->role === 'restaurant_manager' ? 'bg-blue-100 text-blue-800' : 
						   ($user->role === 'courier' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800')) }}">
						{{ $user->role }}
					</span>
				</td>
				<td class="p-3">
					@if($user->subscriptions->count() > 0)
						@foreach($user->subscriptions as $subscription)
							<div class="mb-1">
								<span class="font-medium">{{ $subscription->plan->name }}</span>
								<span class="text-sm text-gray-600">({{ $subscription->plan->price }} so'm)</span>
							</div>
						@endforeach
					@else
						<span class="text-gray-500 text-sm">Tarif yo'q</span>
					@endif
				</td>
				<td class="p-3">
					@if($user->restaurant)
						{{ $user->restaurant->name }}
					@elseif($user->ownedRestaurants->count() > 0)
						@foreach($user->ownedRestaurants as $restaurant)
							<div class="mb-1">{{ $restaurant->name }}</div>
						@endforeach
					@else
						<span class="text-gray-500 text-sm">Restoran yo'q</span>
					@endif
				</td>
				<td class="p-3">
					@if($user->subscriptions->count() > 0)
						@foreach($user->subscriptions as $subscription)
							<div class="mb-1">
								<span class="px-2 py-1 rounded text-xs 
									{{ $subscription->status === 'active' ? 'bg-green-100 text-green-800' : 
									   ($subscription->status === 'expired' ? 'bg-red-100 text-red-800' : 
									   ($subscription->status === 'cancelled' ? 'bg-gray-100 text-gray-800' : 'bg-yellow-100 text-yellow-800')) }}">
									{{ $subscription->status }}
								</span>
								@if($subscription->ends_at)
									<div class="text-xs text-gray-600">{{ $subscription->ends_at->format('Y-m-d') }}</div>
								@endif
							</div>
						@endforeach
					@else
						<span class="text-gray-500 text-sm">-</span>
					@endif
				</td>
				<td class="p-3 text-sm">{{ $user->created_at->format('Y-m-d') }}</td>
				<td class="p-3 text-right">
					<a href="{{ route('super.users.show', $user) }}" class="px-3 py-1 bg-gray-200 rounded">Batafsil</a>
				</td>
			</tr>
			@endforeach
		</tbody>
	</table>
	<div class="mt-4">{{ $users->links() }}</div>
</div>
@endsection 