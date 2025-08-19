<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Restaurant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ProjectController extends Controller
{
	use AuthorizesRequests;

	public function __construct()
	{
		$this->middleware('check.plan.limits:projects')->only(['create', 'store']);
	}

	public function index(Restaurant $restaurant)
	{
		$this->authorize('view', $restaurant);
		
		$projects = Project::where('restaurant_id', $restaurant->id)
			->with(['categories.menuItems'])
			->get();
		
		return view('admin.projects.index', compact('restaurant', 'projects'));
	}

	public function create(Restaurant $restaurant)
	{
		$this->authorize('update', $restaurant);
		
		return view('admin.projects.create', compact('restaurant'));
	}

	public function store(Request $request, Restaurant $restaurant)
	{
		$this->authorize('update', $restaurant);
		
		$request->validate([
			'name' => 'required|string|max:255',
			'description' => 'nullable|string',
			'is_active' => 'boolean'
		]);

		$project = Project::create([
			'restaurant_id' => $restaurant->id,
			'name' => $request->name,
			'description' => $request->description,
			'is_active' => $request->has('is_active')
		]);

		return redirect()->route('admin.projects.index', $restaurant)
			->with('success', 'Loyiha muvaffaqiyatli yaratildi.');
	}

	public function show(Restaurant $restaurant, Project $project)
	{
		$this->authorize('view', $restaurant);
		
		$project->load(['categories.menuItems']);
		
		return view('admin.projects.show', compact('restaurant', 'project'));
	}

	public function edit(Restaurant $restaurant, Project $project)
	{
		$this->authorize('update', $restaurant);
		
		return view('admin.projects.edit', compact('restaurant', 'project'));
	}

	public function update(Request $request, Restaurant $restaurant, Project $project)
	{
		$this->authorize('update', $restaurant);
		
		$request->validate([
			'name' => 'required|string|max:255',
			'description' => 'nullable|string',
			'is_active' => 'boolean'
		]);

		$project->update([
			'name' => $request->name,
			'description' => $request->description,
			'is_active' => $request->has('is_active')
		]);

		return redirect()->route('admin.projects.index', $restaurant)
			->with('success', 'Loyiha muvaffaqiyatli yangilandi.');
	}

	public function destroy(Restaurant $restaurant, Project $project)
	{
		$this->authorize('update', $restaurant);
		
		$project->delete();

		return redirect()->route('admin.projects.index', $restaurant)
			->with('success', 'Loyiha muvaffaqiyatli o\'chirildi.');
	}
} 