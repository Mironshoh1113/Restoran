<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Super Admin
        User::create([
            'name' => 'Super Admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role' => 'super_admin',
            'phone' => '+998901234567',
        ]);

        // Restaurant Manager
        User::create([
            'name' => 'Restaurant Manager',
            'email' => 'manager@example.com',
            'password' => Hash::make('password'),
            'role' => 'restaurant_manager',
            'phone' => '+998901234568',
        ]);

        // Courier
        User::create([
            'name' => 'Courier',
            'email' => 'courier@example.com',
            'password' => Hash::make('password'),
            'role' => 'courier',
            'phone' => '+998901234569',
        ]);
    }
} 