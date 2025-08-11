<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\TelegramUser;
use App\Models\GlobalTelegramUser;
use Illuminate\Support\Facades\DB;

class SetupGlobalTelegram extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'telegram:setup-global {--force : Force recreation of global users}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Setup global telegram users from existing restaurant-specific users';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Setting up global telegram users...');

        if ($this->option('force')) {
            $this->warn('Force option enabled - will recreate all global users');
            GlobalTelegramUser::truncate();
        }

        // Get all unique telegram users across all restaurants
        $uniqueUsers = TelegramUser::select('telegram_id', 'username', 'first_name', 'last_name', 'phone_number', 'language_code', 'is_bot')
            ->groupBy('telegram_id', 'username', 'first_name', 'last_name', 'phone_number', 'language_code', 'is_bot')
            ->get();

        $this->info("Found {$uniqueUsers->count()} unique telegram users");

        $bar = $this->output->createProgressBar($uniqueUsers->count());
        $bar->start();

        $created = 0;
        $updated = 0;

        foreach ($uniqueUsers as $user) {
            try {
                // Get the most recent activity for this user
                $latestActivity = TelegramUser::where('telegram_id', $user->telegram_id)
                    ->orderBy('last_activity', 'desc')
                    ->first();

                $globalUser = GlobalTelegramUser::updateOrCreate(
                    ['telegram_id' => $user->telegram_id],
                    [
                        'username' => $user->username,
                        'first_name' => $user->first_name,
                        'last_name' => $user->last_name,
                        'phone_number' => $user->phone_number,
                        'language_code' => $user->language_code,
                        'is_bot' => $user->is_bot,
                        'last_activity' => $latestActivity ? $latestActivity->last_activity : now(),
                        'is_active' => true,
                    ]
                );

                if ($globalUser->wasRecentlyCreated) {
                    $created++;
                } else {
                    $updated++;
                }

            } catch (\Exception $e) {
                $this->error("Error processing user {$user->telegram_id}: " . $e->getMessage());
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();

        $this->info("Setup completed!");
        $this->info("Created: {$created} global users");
        $this->info("Updated: {$updated} global users");

        // Show some statistics
        $totalGlobal = GlobalTelegramUser::count();
        $totalRestaurant = TelegramUser::count();
        $totalMessages = DB::table('telegram_messages')->count();

        $this->newLine();
        $this->info("Final Statistics:");
        $this->info("- Global Users: {$totalGlobal}");
        $this->info("- Restaurant Users: {$totalRestaurant}");
        $this->info("- Total Messages: {$totalMessages}");

        return Command::SUCCESS;
    }
} 