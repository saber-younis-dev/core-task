<?php

namespace App\Providers;

use App\Events\UserRegistered;
use App\Listeners\LogUserRegistration;
use App\Listeners\NotifyAdmins;
use App\Listeners\SendWelcomeEmail;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        Registered::class => [
            // Laravel's built-in registered event
        ],
        UserRegistered::class => [
            SendWelcomeEmail::class,
            LogUserRegistration::class,
            NotifyAdmins::class,
        ],
        PostSubmittedForApproval::class => [
            NotifyAdminsAboutNewPost::class,
        ],

        PostApproved::class => [
            NotifyAuthorAboutApproval::class,
        ],
    ];

    public function boot(): void
    {
        //
    }
}
