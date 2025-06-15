<?php

namespace App\Notifications;

use App\Models\Post;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PostApprovalStatus extends Notification implements ShouldQueue
{
    use Queueable;

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

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        $mailMessage = (new MailMessage)
            ->subject($this->isApproved ? 'Your Post Has Been Approved' : 'Your Post Was Not Approved')
            ->line("Your post \"{$this->post->title}\" has been reviewed.");

        if ($this->isApproved) {
            $mailMessage->line('Good news! Your post has been approved and is now published.')
                ->action('View Published Post', url("/posts/{$this->post->id}"));
        } else {
            $mailMessage->line('Unfortunately, your post was not approved at this time.')
                ->action('Edit Post', url("/posts/{$this->post->id}/edit"));

            if ($this->feedback) {
                $mailMessage->line('Feedback:')
                    ->line($this->feedback);
            }
        }

        return $mailMessage->line('Thank you for contributing content to our platform!');
    }

    public function toArray($notifiable): array
    {
        return [
            'post_id' => $this->post->id,
            'title' => $this->post->title,
            'approved' => $this->isApproved,
            'reviewer_id' => $this->approvedBy->id,
            'reviewer_name' => $this->approvedBy->name,
            'feedback' => $this->feedback,
            'type' => 'post_review',
        ];
    }
}
