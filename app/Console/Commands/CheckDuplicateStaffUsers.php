<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\StaffUser;

class CheckDuplicateStaffUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'staff:check-duplicates';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check for duplicate staff users by email address';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking for duplicate staff users...');
        
        // Get all staff users
        $staffUsers = StaffUser::all();
        
        // Group by email
        $emailGroups = $staffUsers->groupBy('email');
        
        $duplicates = $emailGroups->filter(function ($group) {
            return $group->count() > 1;
        });
        
        if ($duplicates->isEmpty()) {
            $this->info('No duplicate staff users found.');
            return;
        }
        
        $this->warn('Found duplicate staff users:');
        
        foreach ($duplicates as $email => $users) {
            $this->line("Email: {$email} (Count: {$users->count()})");
            
            foreach ($users as $user) {
                $this->line("  - ID: {$user->_id}, Name: {$user->name}, Status: {$user->status}, Created: {$user->created_at}");
            }
            $this->line('');
        }
        
        $this->info('Total duplicate emails: ' . $duplicates->count());
    }
} 