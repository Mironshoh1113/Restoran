@extends('admin.layouts.app')

@section('content')
<div class="max-w-6xl mx-auto py-6">
	<h1 class="text-xl font-semibold mb-4">Obunalar</h1>
	@if(session('success'))
		<div class="p-3 bg-green-100 text-green-800 rounded mb-3">{{ session('success') }}</div>
	@endif
	@if($errors->any())
		<div class="p-3 bg-red-100 text-red-800 rounded mb-3">
			<ul class="list-disc pl-5">
			@foreach($errors->all() as $error)
				<li>{{ $error }}</li>
			@endforeach
			</ul>
		</div>
	@endif

	<div class="bg-white rounded shadow p-4 mb-6">
		<h2 class="font-medium mb-3">Tarif biriktirish</h2>
		<form method="POST" action="{{ route('super.subscriptions.store') }}" class="grid grid-cols-4 gap-4">
			@csrf
			<div>
				<label class="block mb-1">Restoran</label>
				<select name="restaurant_id" class="w-full border p-2 rounded" required>
					@foreach($restaurants as $r)
						<option value="{{ $r->id }}">{{ $r->name }} ({{ $r->owner?->name }})</option>
					@endforeach
				</select>
			</div>
			<div>
				<label class="block mb-1">Tarif</label>
				<select name="plan_id" class="w-full border p-2 rounded" required>
					@foreach($plans as $p)
						<option value="{{ $p->id }}">{{ $p->name }} - {{ $p->price }}</option>
					@endforeach
				</select>
			</div>
			<div>
				<label class="block mb-1">Boshlanish</label>
				<input type="date" name="starts_at" class="w-full border p-2 rounded" />
			</div>
			<div>
				<label class="block mb-1">Tugash</label>
				<input type="date" name="ends_at" class="w-full border p-2 rounded" />
			</div>
			<div class="col-span-4">
				<button class="px-4 py-2 bg-primary-600 text-white rounded">Biriktirish</button>
			</div>
		</form>
	</div>

	<div class="bg-white rounded shadow p-4 mb-6">
		<h2 class="font-medium mb-3">Admin parolini tiklash</h2>
		<form method="POST" action="{{ route('super.users.reset-password') }}" class="grid grid-cols-3 gap-4">
			@csrf
			<div>
				<label class="block mb-1">Foydalanuvchi</label>
				<select name="user_id" class="w-full border p-2 rounded" required>
					@foreach(\App\Models\User::orderBy('name')->get() as $u)
						<option value="{{ $u->id }}">{{ $u->name }} ({{ $u->email }})</option>
					@endforeach
				</select>
			</div>
			<div>
				<label class="block mb-1">Yangi parol</label>
				<input name="new_password" type="password" class="w-full border p-2 rounded" required />
			</div>
			<div>
				<label class="block mb-1">Tasdiqlash</label>
				<input name="new_password_confirmation" type="password" class="w-full border p-2 rounded" required />
			</div>
			<div class="col-span-3">
				<button class="px-4 py-2 bg-primary-600 text-white rounded">Tiklash</button>
			</div>
		</form>
	</div>

	<table class="w-full bg-white rounded shadow">
		<thead>
			<tr class="text-left border-b">
				<th class="p-3">Restoran</th>
				<th class="p-3">Tarif</th>
				<th class="p-3">Holati</th>
				<th class="p-3">Boshlanish</th>
				<th class="p-3">Tugash</th>
				<th class="p-3"></th>
			</tr>
		</thead>
		<tbody>
			@foreach($subscriptions as $s)
			<tr class="border-b">
				<td class="p-3">{{ $s->restaurant->name }}</td>
				<td class="p-3">{{ $s->plan->name }}</td>
				<td class="p-3">{{ $s->status }}</td>
				<td class="p-3">{{ optional($s->starts_at)->format('Y-m-d') }}</td>
				<td class="p-3">{{ optional($s->ends_at)->format('Y-m-d') }}</td>
				<td class="p-3 text-right">
					<form method="POST" action="{{ route('super.subscriptions.destroy', $s) }}" onsubmit="return confirm('Bekor qilinsinmi?')">
						@csrf
						@method('DELETE')
						<button class="px-3 py-1 bg-red-600 text-white rounded">Bekor qilish</button>
					</form>
				</td>
			</tr>
			@endforeach
		</tbody>
	</table>
	<div class="mt-4">{{ $subscriptions->links() }}</div>
</div>
@endsection 