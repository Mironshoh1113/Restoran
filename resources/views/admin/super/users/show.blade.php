@extends('admin.layouts.app')

@section('content')
<div class="max-w-4xl mx-auto py-6">
	<a href="{{ route('super.users.index') }}" class="text-sm text-gray-600 hover:underline">← Orqaga</a>
	
	<div class="bg-white rounded shadow p-5 mt-3">
		<div class="flex items-center mb-4">
			<div class="w-16 h-16 bg-gradient-to-r from-orange-500 to-red-500 rounded-full flex items-center justify-center mr-4">
				<span class="text-white font-semibold text-2xl">{{ substr($user->name, 0, 1) }}</span>
			</div>
			<div>
				<h1 class="text-2xl font-semibold">{{ $user->name }}</h1>
				<p class="text-gray-600">{{ $user->email }}</p>
				<span class="px-3 py-1 rounded text-sm 
					{{ $user->role === 'super_admin' ? 'bg-purple-100 text-purple-800' : 
					   ($user->role === 'restaurant_manager' ? 'bg-blue-100 text-blue-800' : 
					   ($user->role === 'courier' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800')) }}">
					{{ $user->role }}
				</span>
			</div>
		</div>

		<div class="grid grid-cols-2 gap-4 mb-6">
			<div>
				<span class="font-medium">Ro'yxatdan o'tgan:</span>
				<div class="text-gray-600">{{ $user->created_at->format('Y-m-d H:i') }}</div>
			</div>
			<div>
				<span class="font-medium">So'nggi tashrif:</span>
				<div class="text-gray-600">{{ $user->updated_at->format('Y-m-d H:i') }}</div>
			</div>
		</div>

		@if($user->restaurant)
		<div class="mb-6">
			<h3 class="font-medium mb-2">Tegishli restoran:</h3>
			<div class="p-3 bg-gray-50 rounded">
				<div class="font-medium">{{ $user->restaurant->name }}</div>
				<div class="text-sm text-gray-600">{{ $user->restaurant->address ?? 'Manzil ko\'rsatilmagan' }}</div>
			</div>
		</div>
		@endif

		@if($user->ownedRestaurants->count() > 0)
		<div class="mb-6">
			<h3 class="font-medium mb-2">Egasi bo'lgan restoranlar:</h3>
			<div class="space-y-2">
				@foreach($user->ownedRestaurants as $restaurant)
				<div class="p-3 bg-gray-50 rounded">
					<div class="font-medium">{{ $restaurant->name }}</div>
					<div class="text-sm text-gray-600">{{ $restaurant->address ?? 'Manzil ko\'rsatilmagan' }}</div>
				</div>
				@endforeach
			</div>
		</div>
		@endif

		<div class="mb-6">
			<h3 class="font-medium mb-3">Joriy obunalar:</h3>
			@if($user->subscriptions->count() > 0)
				<div class="space-y-3">
					@foreach($user->subscriptions as $subscription)
					<div class="border rounded p-4">
						<div class="flex items-center justify-between mb-2">
							<div>
								<span class="font-medium">{{ $subscription->plan->name }}</span>
								<span class="text-gray-600">({{ $subscription->plan->price }} so'm)</span>
							</div>
							<span class="px-2 py-1 rounded text-xs 
								{{ $subscription->status === 'active' ? 'bg-green-100 text-green-800' : 
								   ($subscription->status === 'expired' ? 'bg-red-100 text-red-800' : 
								   ($subscription->status === 'cancelled' ? 'bg-gray-100 text-gray-800' : 'bg-yellow-100 text-yellow-800')) }}">
								{{ $subscription->status }}
							</span>
						</div>
						<div class="grid grid-cols-2 gap-4 text-sm">
							<div>
								<span class="font-medium">Boshlanish:</span>
								<div class="text-gray-600">{{ $subscription->starts_at->format('Y-m-d') }}</div>
							</div>
							<div>
								<span class="font-medium">Tugash:</span>
								<div class="text-gray-600">{{ $subscription->ends_at ? $subscription->ends_at->format('Y-m-d') : 'Cheksiz' }}</div>
							</div>
						</div>
						@if($subscription->restaurant)
						<div class="mt-2">
							<span class="font-medium">Restoran:</span>
							<div class="text-gray-600">{{ $subscription->restaurant->name }}</div>
						</div>
						@endif
						
						<form method="POST" action="{{ route('super.subscriptions.update-status', $subscription) }}" class="mt-3 flex items-center gap-2">
							@csrf
							@method('PATCH')
							<select name="status" class="border rounded p-1 text-sm">
								@foreach(['active' => 'Faol', 'expired' => 'Muddati tugagan', 'cancelled' => 'Bekor qilingan', 'suspended' => 'To\'xtatilgan'] as $val => $label)
								<option value="{{ $val }}" @selected($subscription->status === $val)>{{ $label }}</option>
								@endforeach
							</select>
							<button class="px-2 py-1 bg-primary-600 text-white rounded text-sm">Yangilash</button>
						</form>
					</div>
					@endforeach
				</div>
			@else
				<div class="text-gray-500 text-center py-4">Obuna mavjud emas</div>
			@endif
		</div>

		<div class="border-t pt-4">
			<h3 class="font-medium mb-3">Yangi tarif biriktirish:</h3>
			<form method="POST" action="{{ route('super.users.assign-plan', $user) }}" class="grid grid-cols-3 gap-4">
				@csrf
				<div>
					<label class="block mb-1 text-sm">Tarif</label>
					<select name="plan_id" class="w-full border p-2 rounded" required>
						@foreach($plans as $plan)
							<option value="{{ $plan->id }}">{{ $plan->name }} - {{ $plan->price }} so'm</option>
						@endforeach
					</select>
				</div>
				<div>
					<label class="block mb-1 text-sm">Restoran (ixtiyoriy)</label>
					<select name="restaurant_id" class="w-full border p-2 rounded">
						<option value="">Restoran yo'q</option>
						@if($user->ownedRestaurants->count() > 0)
							@foreach($user->ownedRestaurants as $restaurant)
								<option value="{{ $restaurant->id }}">{{ $restaurant->name }}</option>
							@endforeach
						@endif
					</select>
				</div>
				<div>
					<label class="block mb-1 text-sm">Boshlanish sanasi</label>
					<input type="date" name="starts_at" class="w-full border p-2 rounded" value="{{ date('Y-m-d') }}" />
				</div>
				<div class="col-span-3">
					<button class="px-4 py-2 bg-primary-600 text-white rounded">Tarif biriktirish</button>
				</div>
			</form>
		</div>
	</div>
</div>
@endsection 