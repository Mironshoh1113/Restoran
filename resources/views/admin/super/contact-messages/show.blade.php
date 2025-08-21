@extends('admin.layouts.app')

@section('content')
<div class="max-w-3xl mx-auto py-6">
	<a href="{{ route('super.contact-messages.index') }}" class="text-sm text-gray-600 hover:underline">← Orqaga</a>
	<div class="bg-white rounded shadow p-5 mt-3">
		<h1 class="text-xl font-semibold mb-4">Xabar tafsilotlari</h1>
		<div class="space-y-3">
			<div><span class="font-medium">Ism:</span> {{ $contactMessage->name }}</div>
			<div><span class="font-medium">Email:</span> <a href="mailto:{{ $contactMessage->email }}" class="text-blue-600">{{ $contactMessage->email }}</a></div>
			<div><span class="font-medium">Holat:</span>
				<span class="px-2 py-1 rounded text-xs {{ $contactMessage->status === 'new' ? 'bg-blue-100 text-blue-800' : ($contactMessage->status === 'responded' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800') }}">
					{{ $contactMessage->status }}
				</span>
			</div>
			<div><span class="font-medium">Yuborilgan:</span> {{ $contactMessage->created_at->format('Y-m-d H:i') }}</div>
			@if($contactMessage->responded_at)
			<div><span class="font-medium">Javob berilgan:</span> {{ $contactMessage->responded_at->format('Y-m-d H:i') }}</div>
			@endif
			<div class="mt-4">
				<div class="font-medium mb-1">Xabar matni:</div>
				<div class="p-3 bg-gray-50 rounded whitespace-pre-line">{{ $contactMessage->message }}</div>
			</div>
		</div>

		<form method="POST" action="{{ route('super.contact-messages.update-status', $contactMessage) }}" class="mt-5 flex items-center gap-3">
			@csrf
			@method('PATCH')
			<select name="status" class="border rounded p-2">
				@foreach(['new' => 'Yangi', 'read' => "O'qilgan", 'responded' => 'Javob berildi', 'archived' => 'Arxiv'] as $val => $label)
				<option value="{{ $val }}" @selected($contactMessage->status === $val)>{{ $label }}</option>
				@endforeach
			</select>
			<button class="px-4 py-2 bg-primary-600 text-white rounded">Saqlash</button>
		</form>
	</div>
</div>
@endsection 