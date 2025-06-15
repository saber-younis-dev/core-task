<?php

namespace App\Listeners;

use App\Events\UserRegistered;
use App\Models\ActivityLog;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class LogUserRegistration implements ShouldQueue
{
    use InteractsWithQueue;

    public $queue = 'logs';

    public function handle(UserRegistered $event): void
    {
        ActivityLog::create([
            'user_id' => $event->user->id,
            'action' => 'user_registered',
            'description' => "User {$event->user->name} registered with email {$event->user->email}",
            'ip_address' => $event->ipAddress,
            'user_agent' => $event->userAgent,
        ]);
    }
}
