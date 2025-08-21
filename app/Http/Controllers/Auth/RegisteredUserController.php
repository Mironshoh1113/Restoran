<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Plan;
use App\Models\Subscription;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Automatically assign free plan subscription to new users
        $this->assignFreePlanToNewUser($user);

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }

    /**
     * Automatically assign free plan subscription to new users
     */
    private function assignFreePlanToNewUser(User $user): void
    {
        // Find the free plan (0.00 price)
        $freePlan = Plan::where('price', '0.00')
            ->where('is_active', true)
            ->first();

        if ($freePlan) {
            // Create a subscription for the user
            Subscription::create([
                'user_id' => $user->id,
                'restaurant_id' => null, // No restaurant assigned yet
                'plan_id' => $freePlan->id,
                'starts_at' => now(),
                'ends_at' => now()->addDays($freePlan->duration_days),
                'status' => 'active',
                'limits_overrides' => null,
            ]);
        }
    }
}
