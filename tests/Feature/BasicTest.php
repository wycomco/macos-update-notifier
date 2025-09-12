<?php

test('basic application test', function () {
    expect(true)->toBe(true);
});

test('can create user', function () {
    $user = \App\Models\User::factory()->create();
    expect($user)->toBeInstanceOf(\App\Models\User::class);
    expect($user->email)->not->toBeNull();
});

test('can create subscriber', function () {
    $user = \App\Models\User::factory()->create();
    $subscriber = \App\Models\Subscriber::factory()->create([
        'admin_id' => $user->id
    ]);
    
    expect($subscriber)->toBeInstanceOf(\App\Models\Subscriber::class);
    expect($subscriber->admin_id)->toBe($user->id);
});
