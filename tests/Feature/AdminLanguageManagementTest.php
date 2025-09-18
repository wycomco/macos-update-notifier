<?php

use App\Models\Release;
use App\Models\Subscriber;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('admin can see language selection in create subscriber form', function () {
    $user = User::factory()->create(['is_super_admin' => true]);
    
    $response = $this->actingAs($user)->get(route('subscribers.create'));
    
    $response->assertStatus(200)
        ->assertSee('Language')
        ->assertSee('🇺🇸 English')
        ->assertSee('🇩🇪 Deutsch')
        ->assertSee('🇫🇷 Français')
        ->assertSee('🇪🇸 Español');
});

test('admin can create subscriber with specific language', function () {
    $user = User::factory()->create(['is_super_admin' => true]);
    Release::factory()->create(['major_version' => 'macOS 14']);
    
    $response = $this->actingAs($user)->post(route('subscribers.store'), [
        'email' => 'test@example.com',
        'subscribed_versions' => ['macOS 14'],
        'days_to_install' => 3,
        'language' => 'de',
    ]);
    
    $response->assertRedirect();
    
    $subscriber = Subscriber::where('email', 'test@example.com')->first();
    expect($subscriber->language)->toBe('de');
    
    // Should log the creation
    $this->assertDatabaseHas('subscriber_actions', [
        'subscriber_id' => $subscriber->id,
        'action' => 'subscribed',
    ]);
});

test('admin cannot create subscriber with invalid language', function () {
    $user = User::factory()->create(['is_super_admin' => true]);
    Release::factory()->create(['major_version' => 'macOS 14']);
    
    $response = $this->actingAs($user)->post(route('subscribers.store'), [
        'email' => 'test@example.com',
        'subscribed_versions' => ['macOS 14'],
        'days_to_install' => 3,
        'language' => 'invalid',
    ]);
    
    $response->assertSessionHasErrors('language');
    expect(Subscriber::where('email', 'test@example.com')->first())->toBeNull();
});



test('admin can see language selection in edit subscriber form', function () {
    $user = User::factory()->create(['is_super_admin' => true]);
    $subscriber = Subscriber::factory()->create(['language' => 'fr']);
    
    $response = $this->actingAs($user)->get(route('subscribers.edit', $subscriber));
    
    $response->assertStatus(200)
        ->assertSee('Language')
        ->assertSee('🇺🇸 English')
        ->assertSee('🇩🇪 Deutsch')
        ->assertSee('🇫🇷 Français')
        ->assertSee('🇪🇸 Español')
        ->assertSee('selected', false); // Check that French is selected
});

test('admin can update subscriber language', function () {
    $user = User::factory()->create(['is_super_admin' => true]);
    $subscriber = Subscriber::factory()->create(['language' => 'en']);
    
    $response = $this->actingAs($user)->patch(route('subscribers.update', $subscriber), [
        'email' => $subscriber->email,
        'subscribed_versions' => $subscriber->subscribed_versions,
        'days_to_install' => $subscriber->days_to_install,
        'language' => 'es',
    ]);
    
    $response->assertRedirect(route('subscribers.show', $subscriber));
    
    expect($subscriber->fresh()->language)->toBe('es');
    
    // Should log the language change
    $this->assertDatabaseHas('subscriber_actions', [
        'subscriber_id' => $subscriber->id,
        'action' => 'language_changed',
    ]);
});

test('admin cannot update subscriber with invalid language', function () {
    $user = User::factory()->create(['is_super_admin' => true]);
    $subscriber = Subscriber::factory()->create(['language' => 'en']);
    
    $response = $this->actingAs($user)->patch(route('subscribers.update', $subscriber), [
        'email' => $subscriber->email,
        'subscribed_versions' => $subscriber->subscribed_versions,
        'days_to_install' => $subscriber->days_to_install,
        'language' => 'invalid',
    ]);
    
    $response->assertSessionHasErrors('language');
    expect($subscriber->fresh()->language)->toBe('en'); // Should remain unchanged
});

test('subscriber show page displays language with flag', function () {
    $user = User::factory()->create(['is_super_admin' => true]);
    $subscriber = Subscriber::factory()->create(['language' => 'de']);
    
    $response = $this->actingAs($user)->get(route('subscribers.show', $subscriber));
    
    $response->assertStatus(200)
        ->assertSee('Preferred Language')
        ->assertSee('🇩🇪 Deutsch');
});

test('default language is applied when creating subscriber without language', function () {
    $user = User::factory()->create(['is_super_admin' => true]);
    Release::factory()->create(['major_version' => 'macOS 14']);
    
    $response = $this->actingAs($user)->post(route('subscribers.store'), [
        'email' => 'test@example.com',
        'subscribed_versions' => ['macOS 14'],
        'days_to_install' => 7,
        // No language specified
    ]);
    
    $response->assertRedirect(route('subscribers.index'));
    
    $subscriber = Subscriber::where('email', 'test@example.com')->first();
    $defaultLanguage = config('subscriber_languages.default', 'en');
    expect($subscriber->language)->toBe($defaultLanguage);
});