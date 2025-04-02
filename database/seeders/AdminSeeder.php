<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use MongoDB\Laravel\Eloquent\Model;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            '_id' => new \MongoDB\BSON\ObjectId(),
            'name' => 'Admin',
            'email' => 'admin@buksu.edu.ph',
            'password' => Hash::make('49H4gNXGA5'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
} 