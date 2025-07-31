<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run()
    {
        User::create([
            'name' => 'Admin BGH',
            'email' => 'adminbgh@buksu.edu.ph',
            'password' => Hash::make('49H4gNXGA5'),
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        $this->command->info('Admin account created successfully!');
    }
} 