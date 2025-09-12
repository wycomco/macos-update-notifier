<?php

use App\Mail\MacOSUpdateNotification;
use App\Models\Subscriber;
use App\Models\Release;
use Carbon\Carbon;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(TestCase::class, RefreshDatabase::class);

test('macos update notification has correct subject', function () {
    $subscriber = Subscriber::factory()->create([
        'email' => 'test@example.com',
        'days_to_install' => 30,
    ]);

    $release = Release::factory()->create([
        'major_version' => 'macOS 14',
        'version' => '14.6.0',
        'release_date' => Carbon::parse('2025-01-01'),
    ]);

    $mailable = new MacOSUpdateNotification($subscriber, $release);

    expect($mailable->envelope()->subject)->toBe('macOS Update Required: macOS 14 14.6.0');
});

test('macos update notification has correct view', function () {
    $subscriber = Subscriber::factory()->create([
        'email' => 'test@example.com',
        'days_to_install' => 30,
    ]);

    $release = Release::factory()->create([
        'major_version' => 'macOS 14',
        'version' => '14.6.0',
        'release_date' => Carbon::parse('2025-01-01'),
    ]);

    $mailable = new MacOSUpdateNotification($subscriber, $release);
    $content = $mailable->content();

    expect($content->view)->toBe('emails.macos-update-notification');
});

test('macos update notification calculates deadline correctly', function () {
    $subscriber = Subscriber::factory()->create([
        'email' => 'test@example.com',
        'days_to_install' => 30,
    ]);

    $release = Release::factory()->create([
        'major_version' => 'macOS 14',
        'version' => '14.6.0',
        'release_date' => Carbon::parse('2025-01-01'),
    ]);

    $mailable = new MacOSUpdateNotification($subscriber, $release);

    expect($mailable->deadline->format('Y-m-d'))->toBe('2025-01-31');
});

test('macos update notification contains correct data', function () {
    $subscriber = Subscriber::factory()->create([
        'email' => 'test@example.com',
        'days_to_install' => 30,
    ]);

    $release = Release::factory()->create([
        'major_version' => 'macOS 14',
        'version' => '14.6.0',
        'release_date' => Carbon::parse('2025-01-01'),
    ]);

    $mailable = new MacOSUpdateNotification($subscriber, $release);
    $content = $mailable->content();

    expect($content->with)->toHaveKey('subscriber');
    expect($content->with)->toHaveKey('release');
    expect($content->with)->toHaveKey('deadline');
    expect($content->with)->toHaveKey('daysRemaining');
});

test('macos update notification calculates days remaining correctly', function () {
    Carbon::setTestNow(Carbon::parse('2025-01-29'));

    $subscriber = Subscriber::factory()->create([
        'email' => 'test@example.com',
        'days_to_install' => 30,
    ]);

    $release = Release::factory()->create([
        'major_version' => 'macOS 14',
        'version' => '14.6.0',
        'release_date' => Carbon::parse('2025-01-01'), // Deadline: Jan 31
    ]);

    $mailable = new MacOSUpdateNotification($subscriber, $release);
    $content = $mailable->content();

    expect($content->with['daysRemaining'])->toBe(2);
});
