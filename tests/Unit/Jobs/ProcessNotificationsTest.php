<?php

use App\Jobs\ProcessNotifications;
use App\Models\Subscriber;
use App\Models\Release;
use App\Mail\MacOSUpdateNotification;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(TestCase::class, RefreshDatabase::class);

test('process notifications sends email for upcoming deadlines', function () {
    Mail::fake();
    Carbon::setTestNow(Carbon::parse('2025-01-29')); // 2 days before deadline
    
    // Override config to use 2 days warning period for test
    config(['macos_notifier.notification_warning_days' => 2]);

    $subscriber = Subscriber::factory()->create([
        'email' => 'test@example.com',
        'subscribed_versions' => ['macOS 14'],
        'days_to_install' => 30,
    ]);

    $release = Release::factory()->create([
        'major_version' => 'macOS 14',
        'version' => '14.6.0',
        'release_date' => Carbon::parse('2025-01-01'), // Deadline: Jan 31
    ]);

    $job = new ProcessNotifications();
    $job->handle();

    Mail::assertQueued(MacOSUpdateNotification::class, function ($mail) use ($subscriber, $release) {
        return $mail->hasTo($subscriber->email) &&
               $mail->subscriber->id === $subscriber->id &&
               $mail->release->id === $release->id;
    });
});

test('process notifications does not send email when deadline is far away', function () {
    Mail::fake();
    Carbon::setTestNow(Carbon::parse('2025-01-10')); // Far from deadline
    
    // Override config to use 2 days warning period for test
    config(['macos_notifier.notification_warning_days' => 2]);

    $subscriber = Subscriber::factory()->create([
        'subscribed_versions' => ['macOS 14'],
        'days_to_install' => 30,
    ]);

    Release::factory()->create([
        'major_version' => 'macOS 14',
        'version' => '14.6.0',
        'release_date' => Carbon::parse('2025-01-01'), // Deadline: Jan 31
    ]);

    $job = new ProcessNotifications();
    $job->handle();

    Mail::assertNotQueued(MacOSUpdateNotification::class);
});

test('process notifications handles subscribers with no matching releases', function () {
    Mail::fake();

    $subscriber = Subscriber::factory()->create([
        'subscribed_versions' => ['macOS 16'], // No releases for this version
    ]);

    $job = new ProcessNotifications();
    $job->handle();

    Mail::assertNotQueued(MacOSUpdateNotification::class);
});

test('process notifications handles multiple subscribers and releases', function () {
    Mail::fake();
    Carbon::setTestNow(Carbon::parse('2025-01-30')); // 1 day before deadline
    
    // Override config to use 2 days warning period for test
    config(['macos_notifier.notification_warning_days' => 2]);

    $subscriber1 = Subscriber::factory()->create([
        'email' => 'user1@example.com',
        'subscribed_versions' => ['macOS 14'],
        'days_to_install' => 30,
    ]);

    $subscriber2 = Subscriber::factory()->create([
        'email' => 'user2@example.com',
        'subscribed_versions' => ['macOS 15'],
        'days_to_install' => 30,
    ]);

    Release::factory()->create([
        'major_version' => 'macOS 14',
        'release_date' => Carbon::parse('2025-01-01'), // Deadline: Jan 31
    ]);

    Release::factory()->create([
        'major_version' => 'macOS 15',
        'release_date' => Carbon::parse('2025-01-01'), // Deadline: Jan 31
    ]);

    $job = new ProcessNotifications();
    $job->handle();

    Mail::assertQueued(MacOSUpdateNotification::class, 2);
});
