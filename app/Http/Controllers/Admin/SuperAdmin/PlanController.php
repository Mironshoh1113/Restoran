<?php

namespace App\Http\Controllers\Admin\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use Illuminate\Http\Request;

class PlanController extends Controller
{
	public function index()
	{
		$plans = Plan::orderBy('id', 'desc')->paginate(20);
		return view('admin.super.plans.index', compact('plans'));
	}

	public function create()
	{
		return view('admin.super.plans.create');
	}

	public function store(Request $request)
	{
		$validated = $request->validate([
			'name' => 'required|string|max:255',
			'description' => 'nullable|string',
			'price' => 'required|numeric|min:0',
			'duration_days' => 'required|integer|min:1',
			'limits' => 'nullable|array',
			'is_active' => 'boolean',
		]);
		$validated['is_active'] = $request->has('is_active');
		$plan = Plan::create($validated);
		return redirect()->route('super.plans.index')->with('success', 'Tarif yaratildi');
	}

	public function edit(Plan $plan)
	{
		return view('admin.super.plans.edit', compact('plan'));
	}

	public function update(Request $request, Plan $plan)
	{
		$validated = $request->validate([
			'name' => 'required|string|max:255',
			'description' => 'nullable|string',
			'price' => 'required|numeric|min:0',
			'duration_days' => 'required|integer|min:1',
			'limits' => 'nullable|array',
			'is_active' => 'boolean',
		]);
		$validated['is_active'] = $request->has('is_active');
		$plan->update($validated);
		return redirect()->route('super.plans.index')->with('success', 'Tarif yangilandi');
	}

	public function destroy(Plan $plan)
	{
		$plan->delete();
		return redirect()->route('super.plans.index')->with('success', 'Tarif o\'chirildi');
	}
} 