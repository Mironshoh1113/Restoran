@extends('admin.layouts.app')

@section('content')
<div class="max-w-3xl mx-auto py-6">
	<h1 class="text-xl font-semibold mb-4">Tarifni tahrirlash</h1>
	@if($errors->any())
		<div class="p-3 bg-red-100 text-red-800 rounded mb-3">
			<ul class="list-disc pl-5">
			@foreach($errors->all() as $error)
				<li>{{ $error }}</li>
			@endforeach
			</ul>
		</div>
	@endif
	<form method="POST" action="{{ route('super.plans.update', $plan) }}">
		@csrf
		@method('PATCH')
		<div class="grid gap-4">
			<div>
				<label class="block mb-1">Nomi</label>
				<input name="name" class="w-full border p-2 rounded" required value="{{ $plan->name }}" />
			</div>
			<div>
				<label class="block mb-1">Tavsif</label>
				<textarea name="description" class="w-full border p-2 rounded">{{ $plan->description }}</textarea>
			</div>
			<div class="grid grid-cols-3 gap-4">
				<div>
					<label class="block mb-1">Narx</label>
					<input name="price" type="number" step="0.01" min="0" class="w-full border p-2 rounded" value="{{ $plan->price }}" />
				</div>
				<div>
					<label class="block mb-1">Muddat (kun)</label>
					<input name="duration_days" type="number" min="1" class="w-full border p-2 rounded" value="{{ $plan->duration_days }}" />
				</div>
				<div class="flex items-end">
					<label class="inline-flex items-center"><input type="checkbox" name="is_active" value="1" class="mr-2" {{ $plan->is_active ? 'checked' : '' }} /> Faol</label>
				</div>
			</div>
			<div class="border rounded p-4">
				<h2 class="font-medium mb-3">Limitlar</h2>
				@php($limits = $plan->limits ?? [])
				<div class="grid grid-cols-2 gap-4">
					<div>
						<label class="block mb-1">Loyiha soni</label>
						<input name="limits[projects]" type="number" min="0" class="w-full border p-2 rounded" value="{{ $limits['projects'] ?? '' }}" />
					</div>
					<div>
						<label class="block mb-1">Kategoriya soni</label>
						<input name="limits[categories]" type="number" min="0" class="w-full border p-2 rounded" value="{{ $limits['categories'] ?? '' }}" />
					</div>
					<div>
						<label class="block mb-1">Menu taomlari soni</label>
						<input name="limits[menu_items]" type="number" min="0" class="w-full border p-2 rounded" value="{{ $limits['menu_items'] ?? '' }}" />
					</div>
					<div>
						<label class="block mb-1">Kuryerlar soni</label>
						<input name="limits[couriers]" type="number" min="0" class="w-full border p-2 rounded" value="{{ $limits['couriers'] ?? '' }}" />
					</div>
					<div>
						<label class="block mb-1">Restoranlar soni</label>
						<input name="limits[restaurants]" type="number" min="0" class="w-full border p-2 rounded" value="{{ $limits['restaurants'] ?? '' }}" />
					</div>
				</div>
			</div>
			<div class="flex gap-2">
				<button class="px-4 py-2 bg-primary-600 text-white rounded">Saqlash</button>
				<a href="{{ route('super.plans.index') }}" class="px-4 py-2 bg-gray-200 rounded">Bekor qilish</a>
			</div>
		</div>
	</form>
</div>
@endsection 