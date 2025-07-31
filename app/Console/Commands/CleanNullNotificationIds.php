<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Notification;

class CleanNullNotificationIds extends Command
{
    protected $signature = 'notifications:clean-null-ids';
    protected $description = 'Delete notifications with id: null from MongoDB';

    public function handle()
    {
        $count = Notification::whereNull('id')->delete();
        $this->info("Deleted $count notifications with id: null");
    }
} 