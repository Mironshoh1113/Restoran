<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use App\Services\PlanAssignmentService;
use Illuminate\Http\Request;
use Carbon\Carbon;

class UserSubscriptionController extends Controller
{
    public function __construct()
    {
        $this->middleware('super.admin');
    }
    /**
     * Display a listing of users with their subscription information
     */
    public function index()
    {
        $users = User::with(['subscriptions.plan', 'restaurant'])
            ->where('role', '!=', 'super_admin')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $plans = Plan::where('is_active', true)->orderBy('price')->get();
        
        return view('admin.user-subscriptions.index', compact('users', 'plans'));
    }

    /**
     * Show user's subscription details
     */
    public function show(User $user)
    {
        $user->load(['subscriptions.plan', 'restaurant']);
        $currentSubscription = PlanAssignmentService::getCurrentSubscription($user);
        $plans = Plan::where('is_active', true)->orderBy('price')->get();
        
        return view('admin.user-subscriptions.show', compact('user', 'currentSubscription', 'plans'));
    }

    /**
     * Assign a plan to a user
     */
    public function assignPlan(Request $request, User $user)
    {
        $data = $request->validate([
            'plan_id' => 'required|exists:plans,id',
            'starts_at' => 'nullable|date',
            'ends_at' => 'nullable|date|after_or_equal:starts_at',
            'limits_overrides' => 'nullable|array',
        ]);

        $plan = Plan::findOrFail($data['plan_id']);
        $startsAt = !empty($data['starts_at']) ? Carbon::parse($data['starts_at']) : Carbon::now();
        $endsAt = !empty($data['ends_at']) ? Carbon::parse($data['ends_at']) : $startsAt->copy()->addDays($plan->duration_days);

        // Deactivate existing active subscriptions for this user
        Subscription::where('user_id', $user->id)
            ->where('status', 'active')
            ->update(['status' => 'expired']);

        // Create new subscription
        Subscription::create([
            'user_id' => $user->id,
            'restaurant_id' => $user->restaurant_id ?? 1,
            'plan_id' => $data['plan_id'],
            'starts_at' => $startsAt,
            'ends_at' => $endsAt,
            'status' => 'active',
            'limits_overrides' => $data['limits_overrides'] ?? null,
        ]);

        return redirect()->route('admin.user-subscriptions.show', $user)
            ->with('success', 'Tarif muvaffaqiyatli biriktirildi');
    }

    /**
     * Cancel user's subscription
     */
    public function cancelSubscription(User $user)
    {
        $activeSubscription = PlanAssignmentService::getCurrentSubscription($user);
        
        if ($activeSubscription) {
            $activeSubscription->update(['status' => 'cancelled']);
            return redirect()->route('admin.user-subscriptions.show', $user)
                ->with('success', 'Obuna bekor qilindi');
        }

        return redirect()->route('admin.user-subscriptions.show', $user)
            ->with('error', 'Faol obuna topilmadi');
    }

    /**
     * Reset user to free plan
     */
    public function resetToFreePlan(User $user)
    {
        // Cancel current subscription
        $activeSubscription = PlanAssignmentService::getCurrentSubscription($user);
        if ($activeSubscription) {
            $activeSubscription->update(['status' => 'cancelled']);
        }

        // Assign free plan
        PlanAssignmentService::assignFreePlanToNewUser($user);

        return redirect()->route('admin.user-subscriptions.show', $user)
            ->with('success', 'Foydalanuvchi bepul tarifga qaytarildi');
    }
} 