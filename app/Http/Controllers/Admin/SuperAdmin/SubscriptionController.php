<?php

namespace App\Http\Controllers\Admin\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Models\Restaurant;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class SubscriptionController extends Controller
{
	public function index()
	{
		$subscriptions = Subscription::with(['restaurant.owner', 'plan'])->orderByDesc('id')->paginate(30);
		$restaurants = Restaurant::with('owner')->orderBy('name')->get();
		$plans = Plan::where('is_active', true)->orderBy('price')->get();
		return view('admin.super.subscriptions.index', compact('subscriptions', 'restaurants', 'plans'));
	}

	public function store(Request $request)
	{
		$data = $request->validate([
			'restaurant_id' => 'required|exists:restaurants,id',
			'plan_id' => 'required|exists:plans,id',
			'starts_at' => 'nullable|date',
			'ends_at' => 'nullable|date|after_or_equal:starts_at',
			'limits_overrides' => 'nullable|array',
		]);

		$plan = Plan::findOrFail($data['plan_id']);
		$startsAt = !empty($data['starts_at']) ? Carbon::parse($data['starts_at']) : Carbon::now();
		$endsAt = !empty($data['ends_at']) ? Carbon::parse($data['ends_at']) : $startsAt->copy()->addDays($plan->duration_days);

		// deactivate existing active subs for restaurant
		Subscription::where('restaurant_id', $data['restaurant_id'])->where('status', 'active')->update(['status' => 'expired']);

		Subscription::create([
			'user_id' => Restaurant::find($data['restaurant_id'])->owner_user_id,
			'restaurant_id' => $data['restaurant_id'],
			'plan_id' => $data['plan_id'],
			'starts_at' => $startsAt,
			'ends_at' => $endsAt,
			'status' => 'active',
			'limits_overrides' => $data['limits_overrides'] ?? null,
		]);

		return redirect()->route('super.subscriptions.index')->with('success', 'Tarif biriktirildi');
	}

	public function destroy(Subscription $subscription)
	{
		$subscription->status = 'cancelled';
		$subscription->save();
		return redirect()->route('super.subscriptions.index')->with('success', 'Obuna bekor qilindi');
	}

	public function resetPassword(Request $request)
	{
		$validated = $request->validate([
			'user_id' => 'required|exists:users,id',
			'new_password' => 'required|string|min:8|confirmed',
		]);

		$user = User::findOrFail($validated['user_id']);
		$user->password = Hash::make($validated['new_password']);
		$user->save();

		return back()->with('success', 'Parol muvaffaqiyatli tiklandi');
	}
} 