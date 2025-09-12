<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class MagicLinkNotification extends Notification
{
    use Queueable;

    public function __construct(
        public string $loginUrl
    ) {}

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Login to macOS Update Notifier')
            ->view('emails.magic-link', [
                'loginUrl' => $this->loginUrl,
                'user' => $notifiable
            ]);
    }
}
