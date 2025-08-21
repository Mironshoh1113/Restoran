<?php

namespace App\Http\Controllers\Admin\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Plan;
use App\Models\Subscription;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with(['subscriptions' => function($query) {
            $query->where('status', 'active')->with('plan');
        }])
        ->orderBy('created_at', 'desc')
        ->paginate(20);

        $plans = Plan::where('is_active', true)->orderBy('price')->get();
        
        return view('admin.super.users.index', compact('users', 'plans'));
    }

    	public function show(User $user)
	{
		$user->load(['subscriptions.plan', 'restaurant', 'ownedRestaurants']);
		$plans = Plan::where('is_active', true)->orderBy('price')->get();
		
		return view('admin.super.users.show', compact('user', 'plans'));
	}

    public function assignPlan(Request $request, User $user)
    {
        $validated = $request->validate([
            'plan_id' => 'required|exists:plans,id',
            'restaurant_id' => 'nullable|exists:restaurants,id',
            'starts_at' => 'nullable|date',
            'ends_at' => 'nullable|date|after_or_equal:starts_at',
        ]);

        $plan = Plan::findOrFail($validated['plan_id']);
        
        // Deactivate existing active subscriptions for this user/restaurant combination
        $query = Subscription::where('user_id', $user->id);
        if ($validated['restaurant_id']) {
            $query->where('restaurant_id', $validated['restaurant_id']);
        } else {
            $query->whereNull('restaurant_id');
        }
        $query->where('status', 'active')->update(['status' => 'expired']);

        // Create new subscription
        $startsAt = $validated['starts_at'] ? now()->parse($validated['starts_at']) : now();
        $endsAt = $validated['ends_at'] ? now()->parse($validated['ends_at']) : $startsAt->copy()->addDays($plan->duration_days);

        Subscription::create([
            'user_id' => $user->id,
            'restaurant_id' => $validated['restaurant_id'],
            'plan_id' => $plan->id,
            'starts_at' => $startsAt,
            'ends_at' => $endsAt,
            'status' => 'active',
        ]);

        return back()->with('success', 'Tarif muvaffaqiyatli biriktirildi');
    }

    public function updateSubscriptionStatus(Request $request, Subscription $subscription)
    {
        $validated = $request->validate([
            'status' => 'required|in:active,expired,cancelled,suspended'
        ]);

        $subscription->update(['status' => $validated['status']]);

        return back()->with('success', 'Obuna holati yangilandi');
    }
} 