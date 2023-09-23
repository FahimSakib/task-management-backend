<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class sendCommentNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $comment;
    protected $task;

    /**
     * Create a new notification instance.
     */
    public function __construct($comment, $task)
    {
        $this->comment = $comment;
        $this->task    = $task;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Task comment')
            ->line('New comment added by ' . $this->comment->user->name . ' on task: ' . $this->task->name)
            ->line('Thank you.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
