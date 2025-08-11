<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\TelegramUser;
use App\Models\TelegramMessage;
use App\Models\Restaurant;
use Illuminate\Support\Facades\DB;

class FixTelegramUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'telegram:fix-users {--restaurant-id= : Fix users for specific restaurant only}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix telegram users to ensure they appear in all restaurants where they have messages';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Fixing telegram users...');

        $restaurantId = $this->option('restaurant-id');
        
        if ($restaurantId) {
            $this->fixUsersForRestaurant($restaurantId);
        } else {
            $this->fixAllUsers();
        }

        $this->info('Telegram users fixed successfully!');
        return Command::SUCCESS;
    }

    /**
     * Fix users for specific restaurant
     */
    protected function fixUsersForRestaurant($restaurantId)
    {
        $restaurant = Restaurant::find($restaurantId);
        if (!$restaurant) {
            $this->error("Restaurant with ID {$restaurantId} not found");
            return;
        }

        $this->info("Fixing users for restaurant: {$restaurant->name}");

        // Get all users who have messages in this restaurant
        $usersWithMessages = TelegramMessage::where('restaurant_id', $restaurantId)
            ->select('telegram_user_id')
            ->distinct()
            ->get()
            ->pluck('telegram_user_id');

        $this->info("Found {$usersWithMessages->count()} users with messages");

        foreach ($usersWithMessages as $telegramUserId) {
            $this->ensureUserExistsInRestaurant($telegramUserId, $restaurantId);
        }
    }

    /**
     * Fix all users across all restaurants
     */
    protected function fixAllUsers()
    {
        $restaurants = Restaurant::all();
        $this->info("Processing {$restaurants->count()} restaurants");

        foreach ($restaurants as $restaurant) {
            $this->info("Processing restaurant: {$restaurant->name}");
            
            // Get all users who have messages in this restaurant
            $usersWithMessages = TelegramMessage::where('restaurant_id', $restaurant->id)
                ->select('telegram_user_id')
                ->distinct()
                ->get()
                ->pluck('telegram_user_id');

            $this->info("Found {$usersWithMessages->count()} users with messages");

            foreach ($usersWithMessages as $telegramUserId) {
                $this->ensureUserExistsInRestaurant($telegramUserId, $restaurant->id);
            }
        }
    }

    /**
     * Ensure user exists in restaurant
     */
    protected function ensureUserExistsInRestaurant($telegramUserId, $restaurantId)
    {
        // Check if user already exists in this restaurant
        $existingUser = TelegramUser::where('restaurant_id', $restaurantId)
            ->where('telegram_user_id', $telegramUserId)
            ->first();

        if ($existingUser) {
            return; // User already exists
        }

        // Get user info from another restaurant or create basic info
        $userInfo = TelegramUser::where('telegram_user_id', $telegramUserId)
            ->first();

        if ($userInfo) {
            // Create user in this restaurant with same info
            TelegramUser::create([
                'restaurant_id' => $restaurantId,
                'telegram_id' => $userInfo->telegram_id,
                'username' => $userInfo->username,
                'first_name' => $userInfo->first_name,
                'last_name' => $userInfo->last_name,
                'phone_number' => $userInfo->phone_number,
                'language_code' => $userInfo->language_code,
                'is_bot' => $userInfo->is_bot,
                'is_active' => true,
                'last_activity' => $userInfo->last_activity ?? now(),
            ]);

            $this->line("Created user {$userInfo->telegram_id} in restaurant {$restaurantId}");
        } else {
            // Create basic user info
            TelegramUser::create([
                'restaurant_id' => $restaurantId,
                'telegram_id' => $telegramUserId,
                'username' => null,
                'first_name' => 'User',
                'last_name' => null,
                'phone_number' => null,
                'language_code' => 'uz',
                'is_bot' => false,
                'is_active' => true,
                'last_activity' => now(),
            ]);

            $this->line("Created basic user {$telegramUserId} in restaurant {$restaurantId}");
        }
    }
} 