<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Restaurant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class RestaurantController extends Controller
{
    use AuthorizesRequests;

    public function index()
    {
        $user = Auth::user();
        
        if ($user->isSuperAdmin()) {
            $restaurants = Restaurant::with('owner')->get();
        } else {
            $restaurants = Restaurant::where('owner_user_id', $user->id)->get();
        }
        
        return view('admin.restaurants.index', compact('restaurants'));
    }

    public function create()
    {
        return view('admin.restaurants.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'required|string',
            'bot_token' => 'nullable|string',
            'bot_username' => 'nullable|string',
        ]);

        $restaurant = Restaurant::create([
            'name' => $request->name,
            'owner_user_id' => Auth::id(),
            'phone' => $request->phone,
            'address' => $request->address,
            'bot_token' => $request->bot_token,
            'bot_username' => $request->bot_username,
        ]);

        return redirect()->route('admin.restaurants.index')
            ->with('success', 'Restoran muvaffaqiyatli yaratildi.');
    }

    public function show(Restaurant $restaurant)
    {
        $this->authorize('view', $restaurant);
        
        return view('admin.restaurants.show', compact('restaurant'));
    }

    public function edit(Restaurant $restaurant)
    {
        $this->authorize('update', $restaurant);
        
        if (request()->expectsJson()) {
            return response()->json($restaurant);
        }
        
        return view('admin.restaurants.edit', compact('restaurant'));
    }

    public function update(Request $request, Restaurant $restaurant)
    {
        $this->authorize('update', $restaurant);
        
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'required|string',
            'bot_token' => 'nullable|string',
            'bot_username' => 'nullable|string',
        ]);

        try {
            $restaurant->update($request->all());

            if (request()->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Restoran muvaffaqiyatli yangilandi.'
                ]);
            }

            return redirect()->route('admin.restaurants.index')
                ->with('success', 'Restoran muvaffaqiyatli yangilandi.');
                
        } catch (\Exception $e) {
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Restoran yangilashda xatolik yuz berdi: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->route('admin.restaurants.index')
                ->with('error', 'Restoran yangilashda xatolik yuz berdi: ' . $e->getMessage());
        }
    }

    public function destroy(Restaurant $restaurant)
    {
        try {
            $this->authorize('delete', $restaurant);
            
            $restaurant->delete();

            if (request()->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Restoran muvaffaqiyatli o\'chirildi.'
                ]);
            }

            return redirect()->route('admin.restaurants.index')
                ->with('success', 'Restoran muvaffaqiyatli o\'chirildi.');
                
        } catch (\Exception $e) {
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Restoran o\'chirishda xatolik yuz berdi: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->route('admin.restaurants.index')
                ->with('error', 'Restoran o\'chirishda xatolik yuz berdi: ' . $e->getMessage());
        }
    }
} 