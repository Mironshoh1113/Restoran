<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Courier;
use App\Models\Restaurant;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class CourierController extends Controller
{
	use AuthorizesRequests;
	public function __construct()
	{
		$this->middleware('check.plan.limits:couriers')->only(['create', 'store']);
	}
	public function index()
	{
		$user = Auth::user();
		$restaurants = $user->ownedRestaurants()->get();
		$restaurantIds = $restaurants->pluck('id');
		
		$couriers = Courier::whereIn('restaurant_id', $restaurantIds)
			->with('restaurant')
			->latest()
			->paginate(15);
		
		return view('admin.couriers.index', compact('couriers', 'restaurants'));
	}
	
	public function create()
	{
		$user = Auth::user();
		$restaurants = $user->ownedRestaurants()->get();
		
		return view('admin.couriers.create', compact('restaurants'));
	}
	
	public function store(Request $request)
	{
		$request->validate([
			'name' => 'required|string|max:255',
			'phone' => 'required|string|max:20',
			'restaurant_id' => 'required|exists:restaurants,id',
			'is_active' => 'boolean'
		]);
		
		$courier = Courier::create([
			'name' => $request->name,
			'phone' => $request->phone,
			'restaurant_id' => $request->restaurant_id,
			'is_active' => $request->has('is_active')
		]);
		
		return redirect()->route('admin.couriers.index')
			->with('success', 'Kuryer muvaffaqiyatli qo\'shildi');
	}
	
	public function show(Courier $courier)
	{
		$this->authorize('view', $courier);
		
		return view('admin.couriers.show', compact('courier'));
	}
	
	public function edit(Courier $courier)
	{
		$this->authorize('update', $courier);
		
		$user = Auth::user();
		$restaurants = $user->ownedRestaurants()->get();
		
		return view('admin.couriers.edit', compact('courier', 'restaurants'));
	}
	
	public function update(Request $request, Courier $courier)
	{
		$this->authorize('update', $courier);
		
		$request->validate([
			'name' => 'required|string|max:255',
			'phone' => 'required|string|max:20',
			'restaurant_id' => 'required|exists:restaurants,id',
			'is_active' => 'boolean'
		]);
		
		$courier->update([
			'name' => $request->name,
			'phone' => $request->phone,
			'restaurant_id' => $request->restaurant_id,
			'is_active' => $request->has('is_active')
		]);
		
		return redirect()->route('admin.couriers.index')
			->with('success', 'Kuryer muvaffaqiyatli yangilandi');
	}
	
	public function destroy(Courier $courier)
	{
		$this->authorize('delete', $courier);
		
		$courier->delete();
		
		return redirect()->route('admin.couriers.index')
			->with('success', 'Kuryer muvaffaqiyatli o\'chirildi');
	}
} 