<?php

namespace App\Notifications;

use App\Models\Post;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PostPendingApproval extends Notification implements ShouldQueue
{
    use Queueable;

    protected $post;

    public function __construct(Post $post)
    {
        $this->post = $post;
    }

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('New Post Pending Approval')
            ->line('A new post has been submitted for approval.')
            ->line("Title: {$this->post->title}")
            ->line("Author: {$this->post->user->name}")
            ->action('Review Post', url("/admin/posts/{$this->post->id}/review"))
            ->line('Thank you for managing content on our platform!');
    }

    public function toArray($notifiable): array
    {
        return [
            'post_id' => $this->post->id,
            'title' => $this->post->title,
            'author_id' => $this->post->user_id,
            'author_name' => $this->post->user->name,
            'message' => 'A new post requires your approval',
            'type' => 'pending_approval',
        ];
    }
}
