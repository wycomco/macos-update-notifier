<?php

use App\Models\Subscriber;
use App\Models\Release;

test('dashboard displays correctly', function () {
    $user = \App\Models\User::factory()->create();
    $subscriber = Subscriber::factory()->create(['admin_id' => $user->id]);
    $release = Release::factory()->create();

    $response = $this->actingAs($user)->get(route('dashboard'));

    $response->assertStatus(200);
});

test('subscriber creation form validation', function () {
    $user = \App\Models\User::factory()->create();
    
    $response = $this->actingAs($user)->post(route('subscribers.store'), []);

    $response->assertSessionHasErrors(['email', 'subscribed_versions', 'days_to_install']);
});

test('subscriber update preserves data correctly', function () {
    $user = \App\Models\User::factory()->create();
    $subscriber = Subscriber::factory()->create([
        'admin_id' => $user->id,
        'email' => 'original@test.com',
        'subscribed_versions' => ['macOS 14'],
        'days_to_install' => 30,
    ]);

    $updateData = [
        'email' => 'updated@test.com',
        'macos_version' => 'Sonoma',
        'subscribed_versions' => ['macOS 14', 'macOS 15'],
        'days_to_install' => 14,
    ];

    $response = $this->actingAs($user)->put(route('subscribers.update', $subscriber), $updateData);

    $response->assertRedirect();
    
    $subscriber->refresh();
    expect($subscriber->email)->toBe('updated@test.com');
    expect($subscriber->subscribed_versions)->toEqual(['macOS 14', 'macOS 15']);
    expect($subscriber->days_to_install)->toBe(14);
});

test('subscriber show page displays related releases', function () {
    $user = \App\Models\User::factory()->create();
    $subscriber = Subscriber::factory()->create([
        'admin_id' => $user->id,
        'subscribed_versions' => ['macOS 14'],
    ]);

    $release = Release::factory()->create([
        'major_version' => 'macOS 14',
        'version' => '14.6.1',
    ]);

    $response = $this->actingAs($user)->get(route('subscribers.show', $subscriber));

    $response->assertStatus(200)
        ->assertSee($subscriber->email)
        ->assertSee('14.6.1');
});

test('invalid major version is rejected', function () {
    $user = \App\Models\User::factory()->create();
    
    $response = $this->actingAs($user)->post(route('subscribers.store'), [
        'email' => 'test@example.com',
        'subscribed_versions' => ['Windows XP'], // Invalid version
        'days_to_install' => 30,
    ]);

    $response->assertSessionHasErrors('subscribed_versions.0');
});

test('days to install boundaries are enforced', function () {
    $user = \App\Models\User::factory()->create();
    
    // Test minimum boundary
    $response = $this->actingAs($user)->post(route('subscribers.store'), [
        'email' => 'test@example.com',
        'subscribed_versions' => ['macOS 14'],
        'days_to_install' => 0, // Invalid: too low
    ]);

    $response->assertSessionHasErrors('days_to_install');

    // Test maximum boundary
    $response = $this->actingAs($user)->post(route('subscribers.store'), [
        'email' => 'test2@example.com',
        'subscribed_versions' => ['macOS 14'],
        'days_to_install' => 366, // Invalid: too high
    ]);

    $response->assertSessionHasErrors('days_to_install');
});
