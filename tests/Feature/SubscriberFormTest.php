<?php

use App\Models\Release;
use App\Models\Subscriber;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('subscriber edit form displays all required fields', function () {
    $user = User::factory()->create(['is_super_admin' => true]);
    $subscriber = Subscriber::factory()->create();
    
    $response = $this->actingAs($user)->get(route('subscribers.edit', $subscriber));
    
    $response->assertStatus(200)
        ->assertSee('Email Address')
        ->assertSee('Preferred Language')
        ->assertSee('Subscribed macOS Versions')
        ->assertSee('Days to Install After Release')
        ->assertSee('name="email"', false)
        ->assertSee('name="language"', false)
        ->assertSee('name="subscribed_versions[]"', false)
        ->assertSee('name="days_to_install"', false);
});

test('can successfully update subscriber with all fields', function () {
    $user = User::factory()->create(['is_super_admin' => true]);
    $subscriber = Subscriber::factory()->create([
        'email' => 'old@example.com',
        'language' => 'en',
        'days_to_install' => 30,
    ]);
    
    // Create some releases for subscribed_versions validation
    Release::factory()->create(['major_version' => 'macOS 14']);
    Release::factory()->create(['major_version' => 'macOS 15']);
    
    $response = $this->actingAs($user)->patch(route('subscribers.update', $subscriber), [
        'email' => 'updated@example.com',
        'language' => 'fr',
        'subscribed_versions' => ['macOS 14', 'macOS 15'],
        'days_to_install' => 14,
    ]);
    
    $response->assertRedirect(route('subscribers.show', $subscriber));
    
    $subscriber->refresh();
    expect($subscriber->email)->toBe('updated@example.com');
    expect($subscriber->language)->toBe('fr');
    expect($subscriber->subscribed_versions)->toBe(['macOS 14', 'macOS 15']);
    expect($subscriber->days_to_install)->toBe(14);
});

test('validation fails when email is missing', function () {
    $user = User::factory()->create(['is_super_admin' => true]);
    $subscriber = Subscriber::factory()->create();
    
    $response = $this->actingAs($user)->patch(route('subscribers.update', $subscriber), [
        // Missing email
        'language' => $subscriber->language,
        'subscribed_versions' => $subscriber->subscribed_versions,
        'days_to_install' => $subscriber->days_to_install,
    ]);
    
    $response->assertSessionHasErrors('email');
});

test('validation fails when subscribed_versions is empty', function () {
    $user = User::factory()->create(['is_super_admin' => true]);
    $subscriber = Subscriber::factory()->create();
    
    $response = $this->actingAs($user)->patch(route('subscribers.update', $subscriber), [
        'email' => $subscriber->email,
        'language' => $subscriber->language,
        'subscribed_versions' => [], // Empty array
        'days_to_install' => $subscriber->days_to_install,
    ]);
    
    $response->assertSessionHasErrors('subscribed_versions');
});

test('validation fails when days_to_install is out of range', function () {
    $user = User::factory()->create(['is_super_admin' => true]);
    $subscriber = Subscriber::factory()->create();
    
    $response = $this->actingAs($user)->patch(route('subscribers.update', $subscriber), [
        'email' => $subscriber->email,
        'language' => $subscriber->language,
        'subscribed_versions' => $subscriber->subscribed_versions,
        'days_to_install' => 500, // Too high
    ]);
    
    $response->assertSessionHasErrors('days_to_install');
});

test('subscriber create form displays all required fields', function () {
    $user = User::factory()->create(['is_super_admin' => true]);
    
    $response = $this->actingAs($user)->get(route('subscribers.create'));
    
    $response->assertStatus(200)
        ->assertSee('Email Address')
        ->assertSee('Preferred Language')
        ->assertSee('Subscribed macOS Versions')
        ->assertSee('Days to Install After Release')
        ->assertSee('name="email"', false)
        ->assertSee('name="language"', false)
        ->assertSee('name="subscribed_versions[]"', false)
        ->assertSee('name="days_to_install"', false);
});

test('can successfully create subscriber with all fields', function () {
    $user = User::factory()->create(['is_super_admin' => true]);
    
    // Create some releases for subscribed_versions validation
    Release::factory()->create(['major_version' => 'macOS 14']);
    Release::factory()->create(['major_version' => 'macOS 15']);
    
    $response = $this->actingAs($user)->post(route('subscribers.store'), [
        'email' => 'new@example.com',
        'language' => 'de',
        'subscribed_versions' => ['macOS 14'],
        'days_to_install' => 7,
    ]);
    
    $response->assertRedirect(route('subscribers.index'));
    
    $subscriber = Subscriber::where('email', 'new@example.com')->first();
    expect($subscriber)->not->toBeNull();
    expect($subscriber->language)->toBe('de');
    expect($subscriber->subscribed_versions)->toBe(['macOS 14']);
    expect($subscriber->days_to_install)->toBe(7);
});

test('checkbox selections are visually distinct when checked', function () {
    $user = User::factory()->create(['is_super_admin' => true]);
    $subscriber = Subscriber::factory()->create([
        'subscribed_versions' => ['macOS 14', 'macOS 15']
    ]);
    
    $response = $this->actingAs($user)->get(route('subscribers.edit', $subscriber));
    
    $response->assertStatus(200);
    
    // Check that selected checkboxes have the checked attribute
    $content = $response->getContent();
    expect($content)->toContain('checked');
    expect($content)->toContain('peer-checked:bg-purple-500'); // CSS class for checked state
    expect($content)->toContain('peer-checked:border-purple-500'); // Border style for checked state
});

test('language change is logged when updated', function () {
    $user = User::factory()->create(['is_super_admin' => true]);
    $subscriber = Subscriber::factory()->create(['language' => 'en']);
    
    $response = $this->actingAs($user)->patch(route('subscribers.update', $subscriber), [
        'email' => $subscriber->email,
        'language' => 'es', // Changed language
        'subscribed_versions' => $subscriber->subscribed_versions,
        'days_to_install' => $subscriber->days_to_install,
    ]);
    
    $response->assertRedirect(route('subscribers.show', $subscriber));
    
    // Should log the language change
    $this->assertDatabaseHas('subscriber_actions', [
        'subscriber_id' => $subscriber->id,
        'action' => 'language_changed',
    ]);
});

test('subscribed versions change is logged when updated', function () {
    $user = User::factory()->create(['is_super_admin' => true]);
    $subscriber = Subscriber::factory()->create(['subscribed_versions' => ['macOS 14']]);
    
    // Create releases for validation
    Release::factory()->create(['major_version' => 'macOS 14']);
    Release::factory()->create(['major_version' => 'macOS 15']);
    
    $response = $this->actingAs($user)->patch(route('subscribers.update', $subscriber), [
        'email' => $subscriber->email,
        'language' => $subscriber->language,
        'subscribed_versions' => ['macOS 14', 'macOS 15'], // Changed subscribed versions
        'days_to_install' => $subscriber->days_to_install,
    ]);
    
    $response->assertRedirect(route('subscribers.show', $subscriber));
    
    // Should log the subscribed versions change
    $this->assertDatabaseHas('subscriber_actions', [
        'subscriber_id' => $subscriber->id,
        'action' => 'version_changed',
    ]);
});