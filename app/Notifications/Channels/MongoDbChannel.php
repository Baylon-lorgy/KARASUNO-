<?php

namespace App\Notifications\Channels;

use Illuminate\Notifications\Notification;
use App\Models\Notification as NotificationModel;
use Illuminate\Support\Str;

class MongoDbChannel
{
    public function send($notifiable, Notification $notification)
    {
        $data = $notification->toArray($notifiable);

        return NotificationModel::create([
            'id' => Str::uuid()->toString(),
            'type' => get_class($notification),
            'notifiable_type' => get_class($notifiable),
            'notifiable_id' => $notifiable->getKey(),
            'data' => $data,
            'read_at' => null,
        ]);
    }
} 