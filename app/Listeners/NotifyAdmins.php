<?php

namespace App\Listeners;

use App\Events\UserRegistered;
use App\Models\User;
use App\Notifications\NewUserRegistered;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Notification;

class NotifyAdmins implements ShouldQueue
{
    use InteractsWithQueue;

    public $queue = 'notifications';

    public function handle(UserRegistered $event): void
    {
        $admins = User::whereHas('roles', function ($query) {
            $query->where('name', 'admin');
        })->get();

        Notification::send($admins, new NewUserRegistered($event->user));
    }
}
