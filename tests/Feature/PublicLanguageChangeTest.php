<?php

use App\Models\Subscriber;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('subscriber can access language change page with valid token', function () {
    $subscriber = Subscriber::factory()->create(['language' => 'en']);
    $url = $subscriber->getLanguageChangeUrl();
    
    $response = $this->get($url);
    
    $response->assertStatus(200)
        ->assertSee('Change Language')
        ->assertSee('Current Information')
        ->assertSee('🇺🇸 English')
        ->assertSee('Select Language')
        ->assertSee('🇩🇪 Deutsch')
        ->assertSee('🇫🇷 Français')
        ->assertSee('🇪🇸 Español');
});

test('subscriber cannot access language change page with invalid token', function () {
    $response = $this->get(route('public.language-change', 'invalid'));
    
    $response->assertStatus(404); // Invalid token gives 404, not 403
});

test('subscriber can change language successfully', function () {
    $subscriber = Subscriber::factory()->create(['language' => 'en']);
    $url = $subscriber->getLanguageChangeUrl();
    
    $response = $this->post($url, [
        'language' => 'fr',
    ]);
    
    $response->assertRedirect()
        ->assertSessionHas('success');
    
    expect($subscriber->fresh()->language)->toBe('fr');
    
    // Should log the language change
    $this->assertDatabaseHas('subscriber_actions', [
        'subscriber_id' => $subscriber->id,
        'action' => 'language_changed',
    ]);
});

test('subscriber cannot change to invalid language', function () {
    $subscriber = Subscriber::factory()->create(['language' => 'en']);
    $url = $subscriber->getLanguageChangeUrl();
    
    $response = $this->post($url, [
        'language' => 'invalid',
    ]);
    
    $response->assertSessionHasErrors('language');
    expect($subscriber->fresh()->language)->toBe('en'); // Should remain unchanged
});

test('language change page sets correct locale for subscriber language', function () {
    $subscriber = Subscriber::factory()->create(['language' => 'de']);
    $url = $subscriber->getLanguageChangeUrl();
    
    $response = $this->get($url);
    
    $response->assertStatus(200);
    expect(app()->getLocale())->toBe('de');
});

test('changing language updates locale for subsequent requests', function () {
    $subscriber = Subscriber::factory()->create(['language' => 'en']);
    $url = $subscriber->getLanguageChangeUrl();
    
    // Change language to Spanish
    $response = $this->post($url, [
        'language' => 'es',
    ]);
    
    $response->assertRedirect();
    
    // Access the page again - should now use Spanish locale
    $newUrl = $subscriber->fresh()->getLanguageChangeUrl();
    $response = $this->get($newUrl);
    
    $response->assertStatus(200);
    expect(app()->getLocale())->toBe('es');
});

test('language change url contains secure token', function () {
    $subscriber = Subscriber::factory()->create(['language' => 'en']);
    $url = $subscriber->getLanguageChangeUrl();
    
    // URL should contain the unsubscribe token
    expect($url)->toContain('/change-language/')
        ->and($url)->toMatch('/change-language\/[a-zA-Z0-9]+/');
});