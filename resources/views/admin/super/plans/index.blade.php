@extends('admin.layouts.app')

@section('content')
<div class="max-w-5xl mx-auto py-6">
	<div class="flex items-center justify-between mb-4">
		<h1 class="text-xl font-semibold">Tariflar</h1>
		<a href="{{ route('super.plans.create') }}" class="px-4 py-2 bg-primary-600 text-white rounded">Yangi tarif</a>
	</div>
	@if(session('success'))
		<div class="p-3 bg-green-100 text-green-800 rounded mb-3">{{ session('success') }}</div>
	@endif
	<table class="w-full bg-white rounded shadow">
		<thead>
			<tr class="text-left border-b">
				<th class="p-3">Nomi</th>
				<th class="p-3">Narx</th>
				<th class="p-3">Muddat (kun)</th>
				<th class="p-3">Faol</th>
				<th class="p-3"></th>
			</tr>
		</thead>
		<tbody>
			@foreach($plans as $plan)
			<tr class="border-b">
				<td class="p-3">{{ $plan->name }}</td>
				<td class="p-3">{{ $plan->price }}</td>
				<td class="p-3">{{ $plan->duration_days }}</td>
				<td class="p-3">{{ $plan->is_active ? 'Ha' : 'Yo\'q' }}</td>
				<td class="p-3 text-right">
					<a href="{{ route('super.plans.edit', $plan) }}" class="px-3 py-1 bg-gray-200 rounded">Tahrirlash</a>
					<form action="{{ route('super.plans.destroy', $plan) }}" method="POST" class="inline">
						@csrf
						@method('DELETE')
						<button class="px-3 py-1 bg-red-600 text-white rounded" onclick="return confirm('O\'chirishni tasdiqlaysizmi?')">O'chirish</button>
					</form>
				</td>
			</tr>
			@endforeach
		</tbody>
	</table>
	<div class="mt-4">{{ $plans->links() }}</div>
</div>
@endsection 