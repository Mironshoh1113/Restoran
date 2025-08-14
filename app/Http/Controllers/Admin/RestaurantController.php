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
        try {
            $data = $request->only([
                'name',
                'phone',
                'address',
                'description',
                'working_hours',
                'bot_token',
                'bot_username',
                'bot_name',
                'bot_description',
                'is_active',
                'primary_color',
                'secondary_color',
                'accent_color',
                'text_color',
                'bg_color',
                'card_bg',
                'border_radius',
                'shadow',
                'delivery_fee',
                'min_order_amount',
                'payment_methods',
                'social_links',
                'web_app_title',
                'web_app_description',
                'web_app_button_text',
            ]);
            
            // Add owner user ID
            $data['owner_user_id'] = Auth::id();
            
            // Handle checkbox properly
            $data['is_active'] = $request->has('is_active');
            
            // Handle file uploads with 10MB limit
            if ($request->hasFile('logo')) {
                $request->validate([
                    'logo' => 'image|mimes:jpeg,png,jpg,gif|max:10240' // 10MB = 10240 KB
                ]);
                $logoPath = $request->file('logo')->store('restaurants/logos', 'public');
                $data['logo'] = $logoPath;
            }
            
            if ($request->hasFile('bot_image')) {
                $request->validate([
                    'bot_image' => 'image|mimes:jpeg,png,jpg,gif|max:10240' // 10MB = 10240 KB
                ]);
                $botImagePath = $request->file('bot_image')->store('restaurants/bot-images', 'public');
                $data['bot_image'] = $botImagePath;
            }
            
            // Log the creation attempt
            \Log::info('Restaurant creation attempt', [
                'data' => $data,
                'user_id' => auth()->id()
            ]);
            
            $restaurant = Restaurant::create($data);
            
            \Log::info('Restaurant created successfully', [
                'restaurant_id' => $restaurant->id,
                'created_data' => $restaurant->toArray()
            ]);

            if (request()->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Restoran muvaffaqiyatli yaratildi.',
                    'restaurant_id' => $restaurant->id
                ]);
            }

            return redirect()->route('admin.restaurants.index')
                ->with('success', 'Restoran muvaffaqiyatli yaratildi.');
                
        } catch (\Exception $e) {
            \Log::error('Restaurant creation failed', [
                'error' => $e->getMessage(),
                'data' => $request->all()
            ]);
            
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Restoran yaratishda xatolik yuz berdi: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->route('admin.restaurants.index')
                ->with('error', 'Restoran yaratishda xatolik yuz berdi: ' . $e->getMessage());
        }
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

    public function webAppSettings(Restaurant $restaurant)
    {
        $this->authorize('update', $restaurant);
        
        return view('admin.restaurants.web-app-settings', compact('restaurant'));
    }

    public function update(Request $request, Restaurant $restaurant)
    {
        $this->authorize('update', $restaurant);
        
        try {
            // Only update fillable fields
            $data = $request->only([
                'name',
                'phone', 
                'address',
                'description',
                'working_hours',
                'bot_token',
                'bot_username',
                'bot_name',
                'bot_description',
                'is_active',
                'primary_color',
                'secondary_color',
                'accent_color',
                'text_color',
                'bg_color',
                'card_bg',
                'border_radius',
                'shadow',
                'delivery_fee',
                'min_order_amount',
                'payment_methods',
                'social_links',
                'web_app_title',
                'web_app_description',
                'web_app_button_text',
            ]);
            
            // Handle checkbox properly
            $data['is_active'] = $request->has('is_active');
            
            // Handle file uploads with 10MB limit
            if ($request->hasFile('logo')) {
                $request->validate([
                    'logo' => 'image|mimes:jpeg,png,jpg,gif|max:10240' // 10MB = 10240 KB
                ]);
                // Delete old logo if exists
                if ($restaurant->logo) {
                    \Storage::disk('public')->delete($restaurant->logo);
                }
                $logoPath = $request->file('logo')->store('restaurants/logos', 'public');
                $data['logo'] = $logoPath;
            }
            
            if ($request->hasFile('bot_image')) {
                $request->validate([
                    'bot_image' => 'image|mimes:jpeg,png,jpg,gif|max:10240' // 10MB = 10240 KB
                ]);
                // Delete old bot image if exists
                if ($restaurant->bot_image) {
                    \Storage::disk('public')->delete($restaurant->bot_image);
                }
                $botImagePath = $request->file('bot_image')->store('restaurants/bot-images', 'public');
                $data['bot_image'] = $botImagePath;
            }
            
            // Log the update attempt
            \Log::info('Restaurant update attempt', [
                'restaurant_id' => $restaurant->id,
                'data' => $data,
                'user_id' => auth()->id()
            ]);
            
            $restaurant->update($data);
            
            \Log::info('Restaurant updated successfully', [
                'restaurant_id' => $restaurant->id,
                'updated_data' => $restaurant->fresh()->toArray()
            ]);

            if (request()->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Restoran muvaffaqiyatli yangilandi.'
                ]);
            }

            return redirect()->route('admin.restaurants.edit', $restaurant)
                ->with('success', 'Restoran muvaffaqiyatli yangilandi.');
                
        } catch (\Exception $e) {
            \Log::error('Restaurant update failed', [
                'restaurant_id' => $restaurant->id,
                'error' => $e->getMessage(),
                'data' => $request->all()
            ]);
            
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Restoran yangilashda xatolik yuz berdi: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->route('admin.restaurants.edit', $restaurant)
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