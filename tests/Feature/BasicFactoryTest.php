<?php

use App\Models\Subscriber;

test('can create subscriber with factory', function () {
    $subscriber = Subscriber::factory()->make([
        'email' => 'test@example.com',
        'subscribed_versions' => ['macOS 14'],
        'days_to_install' => 30,
    ]);

    expect($subscriber->email)->toBe('test@example.com');
    expect($subscriber->subscribed_versions)->toContain('macOS 14');
    expect($subscriber->days_to_install)->toBe(30);
});

test('basic arithmetic works', function () {
    expect(2 + 2)->toBe(4);
});
