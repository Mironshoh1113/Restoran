<?php

namespace App\Http\Middleware;

use App\Models\Restaurant;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPlanLimits
{
	public function handle(Request $request, Closure $next, string $feature): Response
	{
		$user = $request->user();
		if (!$user || $user->isSuperAdmin()) {
			return $next($request);
		}

		$restaurant = null;
		if ($request->route('restaurant') instanceof Restaurant) {
			$restaurant = $request->route('restaurant');
		} elseif ($request->has('restaurant_id')) {
			$restaurant = Restaurant::find($request->input('restaurant_id'));
		}

		if (!$restaurant) {
			return $next($request);
		}

		$subscription = $user->activeSubscriptionForRestaurant($restaurant->id);
		if (!$subscription || !$subscription->isActive()) {
			return redirect()->back()->withErrors(['plan' => 'Tarif muddati tugagan yoki faol emas. Iltimos tarifni yangilang.']);
		}

		$limits = $subscription->effectiveLimits();
		$limit = $limits[$feature] ?? null;
		if (!$limit) {
			return $next($request);
		}

		$currentCount = match ($feature) {
			'projects' => $restaurant->projects()->count(),
			'categories' => $restaurant->has('projects') ? \App\Models\Category::where('restaurant_id', $restaurant->id)->count() : 0,
			'menu_items' => \App\Models\MenuItem::where('restaurant_id', $restaurant->id)->count(),
			'couriers' => $restaurant->couriers()->count(),
			'restaurants' => $user->ownedRestaurants()->count(),
			'broadcast_per_day' => 0, // placeholder; implement counters if needed
			default => 0,
		};

		if ($currentCount >= (int) $limit) {
			$names = [
				'projects' => 'loyihalar',
				'categories' => 'kategoriyalar',
				'menu_items' => 'taomlar',
				'couriers' => 'kuryerlar',
				'restaurants' => 'restoranlar',
				'broadcast_per_day' => 'efir xabarlari (kunlik)',
			];
			$name = $names[$feature] ?? $feature;
			return redirect()->back()->withErrors(['plan' => "Sizning tarif bo'yicha limit tugagan: $name. Iltimos, tarifni yangilang yoki yangisini tanlang."]);
		}

		return $next($request);
	}
} 