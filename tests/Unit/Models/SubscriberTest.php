<?php

use App\Models\Subscriber;
use Carbon\Carbon;

test('subscriber can check if subscribed to major version', function () {
    $subscriber = new Subscriber([
        'subscribed_versions' => ['macOS 14', 'macOS 15']
    ]);

    expect($subscriber->isSubscribedTo('macOS 14'))->toBeTrue();
    expect($subscriber->isSubscribedTo('macOS 15'))->toBeTrue();
    expect($subscriber->isSubscribedTo('macOS 13'))->toBeFalse();
});

test('subscriber handles empty subscribed versions', function () {
    $subscriber = new Subscriber([
        'subscribed_versions' => null
    ]);

    expect($subscriber->isSubscribedTo('macOS 14'))->toBeFalse();
});

test('subscriber can calculate deadline date', function () {
    $subscriber = new Subscriber([
        'days_to_install' => 30
    ]);

    $releaseDate = Carbon::parse('2025-01-01');
    $expectedDeadline = Carbon::parse('2025-01-31');

    expect($subscriber->getDeadlineDate($releaseDate))->toEqual($expectedDeadline);
});

test('subscriber deadline preserves time', function () {
    $subscriber = new Subscriber([
        'days_to_install' => 5
    ]);

    $releaseDate = Carbon::parse('2025-01-01 14:30:00');
    $deadline = $subscriber->getDeadlineDate($releaseDate);

    expect($deadline->format('Y-m-d H:i:s'))->toBe('2025-01-06 14:30:00');
});

test('subscriber casts subscribed_versions to array', function () {
    $subscriber = new Subscriber();
    $subscriber->subscribed_versions = ['macOS 14', 'macOS 15'];

    expect($subscriber->subscribed_versions)->toBeArray();
    expect($subscriber->subscribed_versions)->toHaveCount(2);
});

test('subscriber casts days_to_install to integer', function () {
    $subscriber = new Subscriber();
    $subscriber->days_to_install = '30';

    expect($subscriber->days_to_install)->toBeInt();
    expect($subscriber->days_to_install)->toBe(30);
});
