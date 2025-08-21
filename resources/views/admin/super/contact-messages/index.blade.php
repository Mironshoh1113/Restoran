@extends('admin.layouts.app')

@section('content')
<div class="max-w-6xl mx-auto py-6">
	<div class="flex items-center justify-between mb-4">
		<h1 class="text-xl font-semibold">Bog'lanish xabarlari</h1>
	</div>
	@if(session('success'))
		<div class="p-3 bg-green-100 text-green-800 rounded mb-3">{{ session('success') }}</div>
	@endif
	<table class="w-full bg-white rounded shadow">
		<thead>
			<tr class="text-left border-b">
				<th class="p-3">Ism</th>
				<th class="p-3">Email</th>
				<th class="p-3">Holat</th>
				<th class="p-3">Vaqt</th>
				<th class="p-3"></th>
			</tr>
		</thead>
		<tbody>
			@foreach($messages as $m)
			<tr class="border-b">
				<td class="p-3">{{ $m->name }}</td>
				<td class="p-3">{{ $m->email }}</td>
				<td class="p-3">
					<span class="px-2 py-1 rounded text-xs {{ $m->status === 'new' ? 'bg-blue-100 text-blue-800' : ($m->status === 'responded' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800') }}">
						{{ $m->status }}
					</span>
				</td>
				<td class="p-3">{{ $m->created_at->format('Y-m-d H:i') }}</td>
				<td class="p-3 text-right">
					<a href="{{ route('super.contact-messages.show', $m) }}" class="px-3 py-1 bg-gray-200 rounded">Ko'rish</a>
					<form action="{{ route('super.contact-messages.destroy', $m) }}" method="POST" class="inline" onsubmit="return confirm('O\'chirilsinmi?')">
						@csrf
						@method('DELETE')
						<button class="px-3 py-1 bg-red-600 text-white rounded">O'chirish</button>
					</form>
				</td>
			</tr>
			@endforeach
		</tbody>
	</table>
	<div class="mt-4">{{ $messages->links() }}</div>
</div>
@endsection 