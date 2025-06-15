<?php

namespace App\Events;

use App\Models\Post;
use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PostApproved
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $post;
    public $approvedBy;

    public function __construct(Post $post, User $approvedBy)
    {
        $this->post = $post;
        $this->approvedBy = $approvedBy;
    }
}
