<?php

namespace App\Jobs;

use App\Models\Release;
use App\Models\Subscriber;
use App\Mail\MacOSUpdateNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class ProcessNotifications implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            Log::info('Starting notification processing');
            
            $subscribers = Subscriber::all();
            $warningDays = config('macos_notifier.notification_warning_days', 2);
            
            foreach ($subscribers as $subscriber) {
                $this->processSubscriberNotifications($subscriber, $warningDays);
            }
            
            Log::info('Completed notification processing');
            
        } catch (\Exception $e) {
            Log::error('Error processing notifications', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    /**
     * Process notifications for a specific subscriber
     */
    private function processSubscriberNotifications(Subscriber $subscriber, int $warningDays): void
    {
        // Skip if subscriber is not active
        if (!$subscriber->isActive()) {
            Log::info("Skipping inactive subscriber", [
                'subscriber_id' => $subscriber->id,
                'email' => $subscriber->email
            ]);
            return;
        }

        foreach ($subscriber->subscribed_versions as $majorVersion) {
            Log::info("Checking major version", [
                'major_version' => $majorVersion,
                'subscriber_id' => $subscriber->id,
                'subscriber_days_to_install' => $subscriber->days_to_install,
                'warning_days' => $warningDays
            ]);

            $latestRelease = Release::getLatestForMajorVersion($majorVersion);
            
            if (!$latestRelease) {
                Log::info("No releases found for major version: {$majorVersion}");
                continue;
            }
            
            // Check if this release is within the warning period
            if ($latestRelease->isWithinDeadline($subscriber->days_to_install, $warningDays)) {
                Log::info("Release within deadline, sending notification", [
                    'subscriber_id' => $subscriber->id,
                    'release_id' => $latestRelease->id,
                    'version' => $latestRelease->version
                ]);
                $this->sendNotification($subscriber, $latestRelease);
            }
        }
    }

    /**
     * Send notification email to subscriber
     */
    private function sendNotification(Subscriber $subscriber, Release $release): void
    {
        try {
            Log::info('Sending notification', [
                'email' => $subscriber->email,
                'version' => $release->version,
                'major_version' => $release->major_version
            ]);
            
            Mail::to($subscriber->email)->queue(
                new MacOSUpdateNotification($subscriber, $release)
            );
            
            // Log the notification action
            $subscriber->logNotificationSent($release);
            
            Log::info('Notification sent successfully', [
                'email' => $subscriber->email,
                'version' => $release->version
            ]);
            
        } catch (\Exception $e) {
            Log::error('Failed to send notification', [
                'email' => $subscriber->email,
                'version' => $release->version,
                'error' => $e->getMessage()
            ]);
        }
    }
}
