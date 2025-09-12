<?php

use App\Models\Release;
use Carbon\Carbon;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(TestCase::class, RefreshDatabase::class);

test('release can find latest for major version', function () {
    // Create multiple releases for macOS 14
    Release::factory()->create([
        'major_version' => 'macOS 14',
        'version' => '14.5.0',
        'release_date' => Carbon::parse('2025-01-01'),
    ]);
    
    $latestRelease = Release::factory()->create([
        'major_version' => 'macOS 14',
        'version' => '14.6.0',
        'release_date' => Carbon::parse('2025-02-01'),
    ]);
    
    Release::factory()->create([
        'major_version' => 'macOS 15',
        'version' => '15.0.0',
        'release_date' => Carbon::parse('2025-01-15'),
    ]);

    $result = Release::getLatestForMajorVersion('macOS 14');

    expect($result->id)->toBe($latestRelease->id);
    expect($result->version)->toBe('14.6.0');
});

test('release returns null when no releases found for major version', function () {
    $result = Release::getLatestForMajorVersion('macOS 16');

    expect($result)->toBeNull();
});

test('release can check if within deadline', function () {
    Carbon::setTestNow(Carbon::parse('2025-01-10'));

    $release = new Release([
        'release_date' => Carbon::parse('2025-01-01'),
    ]);

    // 30 days to install, warning 2 days before deadline
    // Deadline: 2025-01-31, Warning starts: 2025-01-29
    // Current: 2025-01-10 - should NOT be within warning period
    expect($release->isWithinDeadline(30, 2))->toBeFalse();

    // Move closer to deadline
    Carbon::setTestNow(Carbon::parse('2025-01-30'));
    expect($release->isWithinDeadline(30, 2))->toBeTrue();

    // Past deadline
    Carbon::setTestNow(Carbon::parse('2025-02-05'));
    expect($release->isWithinDeadline(30, 2))->toBeFalse();
});

test('release can calculate deadline date', function () {
    $release = new Release([
        'release_date' => Carbon::parse('2025-01-01'),
    ]);

    $deadline = $release->getDeadlineDate(30);

    expect($deadline->format('Y-m-d'))->toBe('2025-01-31');
});

test('release deadline calculation preserves time', function () {
    $release = new Release([
        'release_date' => Carbon::parse('2025-01-01 10:30:45'),
    ]);

    $deadline = $release->getDeadlineDate(15);

    expect($deadline->format('Y-m-d H:i:s'))->toBe('2025-01-16 10:30:45');
});

test('release casts release_date to carbon', function () {
    $release = new Release([
        'release_date' => '2025-01-01',
    ]);

    expect($release->release_date)->toBeInstanceOf(Carbon::class);
});

test('release casts raw_json to array', function () {
    $release = new Release([
        'raw_json' => ['ProductVersion' => '14.6.0', 'Build' => '23G80'],
    ]);

    expect($release->raw_json)->toBeArray();
    expect($release->raw_json['ProductVersion'])->toBe('14.6.0');
});
