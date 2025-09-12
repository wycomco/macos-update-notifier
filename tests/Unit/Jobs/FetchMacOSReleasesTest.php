<?php

use App\Jobs\FetchMacOSReleases;
use App\Models\Release;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(TestCase::class, RefreshDatabase::class);

test('fetch macos releases processes sofa feed correctly', function () {
    $mockResponse = [
        'OSVersions' => [
            [
                'Latest' => [
                    'ProductVersion' => '14.6.1',
                    'ReleaseDate' => '2025-01-15T00:00:00Z',
                    'ProductName' => 'macOS Sonoma',
                ]
            ],
            [
                'Latest' => [
                    'ProductVersion' => '15.0.1',
                    'ReleaseDate' => '2025-01-20T00:00:00Z',
                    'ProductName' => 'macOS Sequoia',
                ]
            ]
        ]
    ];

    Http::fake([
        config('macos_notifier.sofa_feed_url') => Http::response($mockResponse, 200)
    ]);

    $job = new FetchMacOSReleases();
    $job->handle();

    expect(Release::count())->toBe(2);
    
    $macOS14Release = Release::where('major_version', 'macOS 14')->first();
    expect($macOS14Release)->not->toBeNull();
    expect($macOS14Release->version)->toBe('14.6.1');
    
    $macOS15Release = Release::where('major_version', 'macOS 15')->first();
    expect($macOS15Release)->not->toBeNull();
    expect($macOS15Release->version)->toBe('15.0.1');
});

test('fetch macos releases handles http failures gracefully', function () {
    Http::fake([
        config('macos_notifier.sofa_feed_url') => Http::response('Server Error', 500)
    ]);

    $job = new FetchMacOSReleases();
    $job->handle();

    // Should not create any releases on failure
    expect(Release::count())->toBe(0);
});

test('fetch macos releases handles invalid json gracefully', function () {
    Http::fake([
        config('macos_notifier.sofa_feed_url') => Http::response(['invalid' => 'data'], 200)
    ]);

    $job = new FetchMacOSReleases();
    $job->handle();

    // Should not create any releases with invalid data
    expect(Release::count())->toBe(0);
});

test('fetch macos releases updates existing releases', function () {
    // Create an existing release
    $existingRelease = Release::factory()->create([
        'major_version' => 'macOS 14',
        'version' => '14.6.0',
        'release_date' => '2025-01-01',
    ]);

    $mockResponse = [
        'OSVersions' => [
            [
                'Latest' => [
                    'ProductVersion' => '14.6.0', // Same version
                    'ReleaseDate' => '2025-01-15T00:00:00Z', // Different date
                    'ProductName' => 'macOS Sonoma',
                ]
            ]
        ]
    ];

    Http::fake([
        config('macos_notifier.sofa_feed_url') => Http::response($mockResponse, 200)
    ]);

    $job = new FetchMacOSReleases();
    $job->handle();

    // Should still have only 1 release but with updated data
    expect(Release::count())->toBe(1);
    
    $existingRelease->refresh();
    expect($existingRelease->release_date->format('Y-m-d'))->toBe('2025-01-15');
});

test('fetch macos releases handles missing release date', function () {
    $mockResponse = [
        'OSVersions' => [
            [
                'Latest' => [
                    'ProductVersion' => '14.6.1',
                    // No ReleaseDate field
                    'ProductName' => 'macOS Sonoma',
                ]
            ]
        ]
    ];

    Http::fake([
        config('macos_notifier.sofa_feed_url') => Http::response($mockResponse, 200)
    ]);

    $job = new FetchMacOSReleases();
    $job->handle();

    // Should still create a release with current date as fallback
    expect(Release::count())->toBe(1);
    
    $release = Release::first();
    expect($release->release_date->isToday())->toBeTrue();
});
