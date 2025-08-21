<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\PlanAssignmentService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class TestUserRegistration extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:user-registration {email} {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test user registration with automatic plan assignment';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');
        $name = $this->argument('name');

        // Check if user already exists
        if (User::where('email', $email)->exists()) {
            $this->error("User with email {$email} already exists!");
            return 1;
        }

        $this->info("Creating test user: {$name} ({$email})");

        // Create user (simulating registration)
        $user = User::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make('password'),
        ]);

        $this->info("User created with ID: {$user->id}");

        // Automatically assign free plan
        $subscription = PlanAssignmentService::assignFreePlanToNewUser($user);

        if ($subscription) {
            $this->info("Free plan assigned successfully!");
            $this->info("Plan: {$subscription->plan->name}");
            $this->info("Price: {$subscription->plan->price} so'm");
            $this->info("Status: {$subscription->status}");
            $this->info("Starts: {$subscription->starts_at}");
            $this->info("Ends: {$subscription->ends_at}");
            $this->info("Restaurant ID: {$subscription->restaurant_id}");
        } else {
            $this->error("Failed to assign free plan!");
            return 1;
        }

        $this->info("Test completed successfully!");
        return 0;
    }
} 