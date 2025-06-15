<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewUserRegistered extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public User $newUser
    ) {}

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('New User Registration')
            ->line("A new user has registered on " . config('app.name'))
            ->line("Name: {$this->newUser->name}")
            ->line("Email: {$this->newUser->email}")
            ->action('View User', url("/admin/users/{$this->newUser->id}"))
            ->line('Thank you for using our application!');
    }

    public function toArray($notifiable): array
    {
        return [
            'message' => "New user registered: {$this->newUser->name}",
            'user_id' => $this->newUser->id,
            'user_email' => $this->newUser->email,
        ];
    }
}
