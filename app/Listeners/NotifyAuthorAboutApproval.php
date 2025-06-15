<?php

namespace App\Listeners;

use App\Events\PostApproved;
use App\Jobs\SendPostApprovalResultToAuthor;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class NotifyAuthorAboutApproval implements ShouldQueue
{
    use InteractsWithQueue;

    public $queue = 'notifications';

    public function handle(PostApproved $event): void
    {
        // Dispatch the job to notify the author
        SendPostApprovalResultToAuthor::dispatch(
            $event->post,
            $event->approvedBy,
            $event->post->status === 'published',
            null // No feedback in this case
        );
    }
}
