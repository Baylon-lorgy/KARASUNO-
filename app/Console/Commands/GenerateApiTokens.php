<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;

class GenerateApiTokens extends Command
{
    protected $signature = 'users:generate-tokens';
    protected $description = 'Generate API tokens for all users';

    public function handle()
    {
        $users = User::whereNull('api_token')->get();
        
        if ($users->isEmpty()) {
            $this->info('All users already have API tokens.');
            return;
        }

        $this->info("Generating API tokens for {$users->count()} users...");
        
        $bar = $this->output->createProgressBar($users->count());
        
        foreach ($users as $user) {
            $user->generateApiToken();
            $bar->advance();
        }
        
        $bar->finish();
        $this->newLine();
        $this->info('API tokens generated successfully!');
    }
} 