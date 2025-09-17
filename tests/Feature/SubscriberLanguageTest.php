<?php

use App\Models\Subscriber;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('getLanguageDisplayName returns correct display name for supported languages', function () {
    $subscriber = new Subscriber(['language' => 'en']);
    expect($subscriber->getLanguageDisplayName())->toBe('English');

    $subscriber = new Subscriber(['language' => 'de']);
    expect($subscriber->getLanguageDisplayName())->toBe('Deutsch');

    $subscriber = new Subscriber(['language' => 'fr']);
    expect($subscriber->getLanguageDisplayName())->toBe('Français');

    $subscriber = new Subscriber(['language' => 'es']);
    expect($subscriber->getLanguageDisplayName())->toBe('Español');
});

test('getLanguageDisplayName returns fallback for unsupported language', function () {
    $subscriber = new Subscriber(['language' => 'unknown']);
    expect($subscriber->getLanguageDisplayName())->toBe('Unknown');
});

test('getLanguageDisplayName handles null language', function () {
    $subscriber = new Subscriber(['language' => null]);
    expect($subscriber->getLanguageDisplayName())->toBe('Unknown');
});

test('getLanguageFlag returns correct flag for supported languages', function () {
    $subscriber = new Subscriber(['language' => 'en']);
    expect($subscriber->getLanguageFlag())->toBe('🇺🇸');

    $subscriber = new Subscriber(['language' => 'de']);
    expect($subscriber->getLanguageFlag())->toBe('🇩🇪');

    $subscriber = new Subscriber(['language' => 'fr']);
    expect($subscriber->getLanguageFlag())->toBe('🇫🇷');

    $subscriber = new Subscriber(['language' => 'es']);
    expect($subscriber->getLanguageFlag())->toBe('🇪🇸');
});

test('updateLanguage successfully updates subscriber language', function () {
    $subscriber = Subscriber::factory()->create(['language' => 'en']);
    
    $subscriber->updateLanguage('de');
    
    expect($subscriber->fresh()->language)->toBe('de');
});

test('updateLanguage logs subscriber action', function () {
    $subscriber = Subscriber::factory()->create(['language' => 'en']);
    
    $subscriber->updateLanguage('fr');
    
    $this->assertDatabaseHas('subscriber_actions', [
        'subscriber_id' => $subscriber->id,
        'action' => 'language_changed',
        'data' => json_encode([
            'old_language' => 'en',
            'new_language' => 'fr'
        ]),
    ]);
});

test('updateLanguage handles same language', function () {
    $subscriber = Subscriber::factory()->create(['language' => 'en']);
    
    $subscriber->updateLanguage('en');
    
    // Should still log the action even if language is the same
    $this->assertDatabaseHas('subscriber_actions', [
        'subscriber_id' => $subscriber->id,
        'action' => 'language_changed',
        'data' => json_encode([
            'old_language' => 'en',
            'new_language' => 'en'
        ]),
    ]);
});

test('getLanguageChangeUrl returns correct url', function () {
    $subscriber = Subscriber::factory()->create();
    
    $url = $subscriber->getLanguageChangeUrl();
    
    $expectedUrl = route('public.language-change', ['token' => $subscriber->unsubscribe_token]);
    expect($url)->toBe($expectedUrl);
});

test('subscriber gets default language on creation', function () {
    $subscriber = Subscriber::factory()->create();
    
    $defaultLanguage = config('subscriber_languages.default', 'en');
    expect($subscriber->language)->toBe($defaultLanguage);
});

test('subscriber can be created with specific language', function () {
    $subscriber = Subscriber::factory()->create(['language' => 'de']);
    
    expect($subscriber->language)->toBe('de');
});

test('language validation rule matches config', function () {
    $validationRule = config('subscriber_languages.validation_rule');
    $supportedLanguages = array_keys(config('subscriber_languages.supported'));
    
    // Extract allowed languages from validation rule
    preg_match('/in:([a-z,]+)/', $validationRule, $matches);
    $allowedLanguages = explode(',', $matches[1]);
    
    expect($allowedLanguages)->toEqual($supportedLanguages);
});