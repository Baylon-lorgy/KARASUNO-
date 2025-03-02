<?php

namespace App\Console\Commands;

use App\Models\Admin;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class CreateAdminUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:create';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new admin user';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->ask('What is the admin email?');
        $name = $this->ask('What is the admin name?');
        $password = $this->secret('What is the admin password?');

        try {
            $admin = Admin::create([
                'name' => $name,
                'email' => $email,
                'password' => Hash::make($password),
                'role' => 'admin',
                'status' => 'active'
            ]);

            $this->info('Admin user created successfully!');
            $this->table(
                ['Name', 'Email'],
                [[$admin->name, $admin->email]]
            );
        } catch (\Exception $e) {
            $this->error('Failed to create admin user: ' . $e->getMessage());
        }
    }
}
