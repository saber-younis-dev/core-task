<?php

namespace App\Listeners;

use App\Events\PostSubmittedForApproval;
use App\Jobs\SendPostApprovalRequestToAdmins;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class NotifyAdminsAboutNewPost implements ShouldQueue
{
    use InteractsWithQueue;

    public $queue = 'notifications';

    public function handle(PostSubmittedForApproval $event): void
    {
        // Dispatch the job to notify admins
        SendPostApprovalRequestToAdmins::dispatch($event->post);
    }
}
