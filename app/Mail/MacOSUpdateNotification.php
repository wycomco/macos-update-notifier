<?php

namespace App\Mail;

use App\Models\Subscriber;
use App\Models\Release;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Carbon\Carbon;

class MacOSUpdateNotification extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public Subscriber $subscriber;
    public Release $release;
    public Carbon $deadline;

    /**
     * Create a new message instance.
     */
    public function __construct(Subscriber $subscriber, Release $release)
    {
        $this->subscriber = $subscriber;
        $this->release = $release;
        $this->deadline = $release->getDeadlineDate($subscriber->days_to_install);
        
        // Set the locale for this notification
        app()->setLocale($subscriber->language);
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $replyTo = $this->subscriber->admin?->email ?? config('mail.from.address');
        
        return new Envelope(
            subject: __('emails.macos_update.subject', [
                'version' => $this->release->major_version,
                'build' => $this->release->version
            ]),
            replyTo: [$replyTo],
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.macos-update-notification',
            with: [
                'subscriber' => $this->subscriber,
                'release' => $this->release,
                'deadline' => $this->deadline,
                'daysRemaining' => (int) Carbon::now()->diffInDays($this->deadline, false),
                'unsubscribeUrl' => $this->subscriber->getUnsubscribeUrl(),
                'versionChangeUrl' => $this->subscriber->getVersionChangeUrl(),
                'languageChangeUrl' => $this->subscriber->getLanguageChangeUrl(),
                'adminEmail' => $this->subscriber->admin?->email,
            ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
