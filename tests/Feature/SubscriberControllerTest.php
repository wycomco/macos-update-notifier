<?php

use App\Models\Subscriber;
use App\Models\Release;
use App\Models\User;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});

test('subscriber index page displays correctly', function () {
    // Create a release to ensure version is available
    Release::factory()->create(['major_version' => 'macOS 14']);
    
    $subscriber = Subscriber::factory()->create([
        'admin_id' => $this->user->id,
        'email' => 'test@example.com',
        'subscribed_versions' => ['macOS 14'],
        'days_to_install' => 30,
    ]);

    $response = $this->get(route('subscribers.index'));

    $response->assertStatus(200)
        ->assertSee('test@example.com')
        ->assertSee('macOS 14');
});

test('can create new subscriber', function () {
    // Create releases to ensure versions are available
    Release::factory()->create(['major_version' => 'macOS 14']);
    Release::factory()->create(['major_version' => 'macOS 15']);
    
    $subscriberData = [
        'email' => 'new@example.com',
        'subscribed_versions' => ['macOS 14', 'macOS 15'],
        'days_to_install' => 30,
    ];

    $response = $this->post(route('subscribers.store'), $subscriberData);

    $response->assertRedirect(route('subscribers.index'))
        ->assertSessionHas('success');

    $this->assertDatabaseHas('subscribers', [
        'email' => 'new@example.com',
        'days_to_install' => 30,
    ]);

    $subscriber = Subscriber::where('email', 'new@example.com')->first();
    expect($subscriber->subscribed_versions)->toEqual(['macOS 14', 'macOS 15']);
});

test('email validation works on subscriber creation', function () {
    // Create a release to ensure version is available
    Release::factory()->create(['major_version' => 'macOS 14']);
    
    $subscriberData = [
        'email' => 'invalid-email',
        'subscribed_versions' => ['macOS 14'],
        'days_to_install' => 30,
    ];

    $response = $this->post(route('subscribers.store'), $subscriberData);

    $response->assertSessionHasErrors('email');
});

test('unique email validation works on subscriber creation', function () {
    // Create a release to ensure version is available
    Release::factory()->create(['major_version' => 'macOS 14']);
    
    Subscriber::factory()->create([
        'admin_id' => $this->user->id,
        'email' => 'existing@example.com'
    ]);

    $subscriberData = [
        'email' => 'existing@example.com',
        'subscribed_versions' => ['macOS 14'],
        'days_to_install' => 30,
    ];

    $response = $this->post(route('subscribers.store'), $subscriberData);

    $response->assertSessionHasErrors('email');
});

test('can view subscriber details', function () {
    // Create a release to ensure version is available
    Release::factory()->create(['major_version' => 'macOS 14']);
    
    $subscriber = Subscriber::factory()->create([
        'admin_id' => $this->user->id,
        'email' => 'test@example.com',
        'subscribed_versions' => ['macOS 14'],
        'days_to_install' => 30,
    ]);

    $response = $this->get(route('subscribers.show', $subscriber));

    $response->assertStatus(200)
        ->assertSee('test@example.com')
        ->assertSee('macOS 14')
        ->assertSee('30 days');
});

test('can edit subscriber', function () {
    // Create a release to ensure version is available
    Release::factory()->create(['major_version' => 'macOS 14']);
    
    $subscriber = Subscriber::factory()->create([
        'admin_id' => $this->user->id,
        'email' => 'original@example.com',
        'subscribed_versions' => ['macOS 14'],
        'days_to_install' => 30,
    ]);

    $response = $this->get(route('subscribers.edit', $subscriber));

    $response->assertStatus(200)
        ->assertSee('original@example.com');
});

test('can update subscriber', function () {
    // Create releases to ensure versions are available
    Release::factory()->create(['major_version' => 'macOS 14']);
    Release::factory()->create(['major_version' => 'macOS 15']);
    
    $subscriber = Subscriber::factory()->create([
        'admin_id' => $this->user->id,
        'email' => 'original@example.com',
        'subscribed_versions' => ['macOS 14'],
        'days_to_install' => 30,
    ]);

    $updateData = [
        'email' => 'updated@example.com',
        'macos_version' => 'Sonoma',
        'subscribed_versions' => ['macOS 14', 'macOS 15'],
        'days_to_install' => 14,
    ];

    $response = $this->put(route('subscribers.update', $subscriber), $updateData);

    $response->assertRedirect(route('subscribers.show', $subscriber))
        ->assertSessionHas('success');

    $this->assertDatabaseHas('subscribers', [
        'id' => $subscriber->id,
        'email' => 'updated@example.com',
        'days_to_install' => 14,
    ]);

    $subscriber->refresh();
    expect($subscriber->subscribed_versions)->toEqual(['macOS 14', 'macOS 15']);
});

test('email uniqueness validation works on update but ignores current subscriber', function () {
    // Create a release to ensure version is available
    Release::factory()->create(['major_version' => 'macOS 14']);
    
    $subscriber1 = Subscriber::factory()->create([
        'admin_id' => $this->user->id,
        'email' => 'first@example.com'
    ]);
    $subscriber2 = Subscriber::factory()->create([
        'admin_id' => $this->user->id,
        'email' => 'second@example.com'
    ]);

    // Try to update subscriber2 with subscriber1's email - should fail
    $response = $this->put(route('subscribers.update', $subscriber2), [
        'email' => 'first@example.com',
        'macos_version' => 'Sonoma',
        'subscribed_versions' => ['macOS 14'],
        'days_to_install' => 30,
    ]);

    $response->assertSessionHasErrors('email');

    // Update subscriber2 with its own email - should work
    $response = $this->put(route('subscribers.update', $subscriber2), [
        'email' => 'second@example.com',
        'macos_version' => 'Sonoma',
        'subscribed_versions' => ['macOS 14'],
        'days_to_install' => 30,
    ]);

    $response->assertRedirect(route('subscribers.show', $subscriber2));
});

test('can delete subscriber', function () {
    $subscriber = Subscriber::factory()->create([
        'admin_id' => $this->user->id
    ]);

    $response = $this->delete(route('subscribers.destroy', $subscriber));

    $response->assertRedirect(route('subscribers.index'))
        ->assertSessionHas('success');

    $this->assertDatabaseMissing('subscribers', ['id' => $subscriber->id]);
});

test('subscribed versions validation requires at least one version', function () {
    // Create a release to ensure version is available
    Release::factory()->create(['major_version' => 'macOS 14']);
    
    $subscriberData = [
        'email' => 'test@example.com',
        'subscribed_versions' => [],
        'days_to_install' => 30,
    ];

    $response = $this->post(route('subscribers.store'), $subscriberData);

    $response->assertSessionHasErrors('subscribed_versions');
});

test('days to install validation works', function () {
    // Create a release to ensure version is available
    Release::factory()->create(['major_version' => 'macOS 14']);
    
    $subscriberData = [
        'email' => 'test@example.com',
        'subscribed_versions' => ['macOS 14'],
        'days_to_install' => 0, // Invalid: should be at least 1
    ];

    $response = $this->post(route('subscribers.store'), $subscriberData);

    $response->assertSessionHasErrors('days_to_install');

    $subscriberData['days_to_install'] = 400; // Invalid: should be max 365

    $response = $this->post(route('subscribers.store'), $subscriberData);

    $response->assertSessionHasErrors('days_to_install');
});

test('available versions are dynamically loaded from releases', function () {
    // Create releases with different major versions
    Release::factory()->create(['major_version' => 'macOS 13']);
    Release::factory()->create(['major_version' => 'macOS 14']);
    Release::factory()->create(['major_version' => 'macOS 15']);

    $response = $this->get(route('subscribers.create'));

    $response->assertStatus(200)
        ->assertSee('macOS 13')
        ->assertSee('macOS 14')
        ->assertSee('macOS 15');
});

test('validation accepts any version that exists in releases', function () {
    // Create a custom release version
    Release::factory()->create(['major_version' => 'macOS 13']);
    
    $subscriberData = [
        'email' => 'test@example.com',
        'subscribed_versions' => ['macOS 13'],
        'days_to_install' => 30,
    ];

    $response = $this->post(route('subscribers.store'), $subscriberData);

    $response->assertRedirect(route('subscribers.index'))
        ->assertSessionHas('success');
});

test('validation rejects versions that do not exist in releases', function () {
    // Create only macOS 14, but try to subscribe to macOS 16
    Release::factory()->create(['major_version' => 'macOS 14']);
    
    $subscriberData = [
        'email' => 'test@example.com',
        'subscribed_versions' => ['macOS 16'], // This version doesn't exist
        'days_to_install' => 30,
    ];

    $response = $this->post(route('subscribers.store'), $subscriberData);

    $response->assertSessionHasErrors('subscribed_versions.0');
});

test('fallback versions work when no releases exist', function () {
    // Don't create any releases, should fall back to default versions
    $subscriberData = [
        'email' => 'test@example.com',
        'subscribed_versions' => ['macOS 14'], // Should work because of fallback
        'days_to_install' => 30,
    ];

    $response = $this->post(route('subscribers.store'), $subscriberData);

    $response->assertRedirect(route('subscribers.index'))
        ->assertSessionHas('success');
});
