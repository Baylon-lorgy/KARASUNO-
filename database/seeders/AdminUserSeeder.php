<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Admin',
            'email' => 'admin@buksu.edu.ph',
            'password' => Hash::make('49H4gNXGA5'),
            'email_verified_at' => now(),
        ]);
    }
} 