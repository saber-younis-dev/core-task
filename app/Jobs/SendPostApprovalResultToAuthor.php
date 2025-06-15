<?php

namespace App\Jobs;

use App\Models\Post;
use App\Models\User;
use App\Notifications\PostApprovalStatus;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendPostApprovalResultToAuthor implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $post;
    protected $approvedBy;
    protected $isApproved;
    protected $feedback;

    public function __construct(Post $post, User $approvedBy, bool $isApproved, ?string $feedback = null)
    {
        $this->post = $post;
        $this->approvedBy = $approvedBy;
        $this->isApproved = $isApproved;
        $this->feedback = $feedback;
    }

    public function handle(): void
    {
        // Get the post author
        $author = $this->post->user;

        // Send notification to the author
        $author->notify(new PostApprovalStatus(
            $this->post,
            $this->approvedBy,
            $this->isApproved,
            $this->feedback
        ));
    }
}
