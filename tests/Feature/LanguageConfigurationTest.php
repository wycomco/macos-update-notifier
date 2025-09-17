<?php

test('supported languages are properly configured', function () {
    $supportedLanguages = config('subscriber_languages.supported');
    
    expect($supportedLanguages)->toBeArray()
        ->and($supportedLanguages)->toHaveKeys(['en', 'de', 'fr', 'es']);
    
    // Check each language has required structure
    foreach ($supportedLanguages as $code => $language) {
        expect($language)->toHaveKeys(['name', 'flag'])
            ->and($language['name'])->toBeString()
            ->and($language['flag'])->toBeString();
    }
});

test('default language configuration is valid', function () {
    $defaultLanguage = config('subscriber_languages.default');
    $supportedLanguages = config('subscriber_languages.supported');
    
    expect($defaultLanguage)->toBeString()
        ->and($supportedLanguages)->toHaveKey($defaultLanguage);
});

test('validation rule includes all supported languages', function () {
    $validationRule = config('subscriber_languages.validation_rule');
    $supportedLanguages = array_keys(config('subscriber_languages.supported'));
    
    expect($validationRule)->toBeString()
        ->and($validationRule)->toContain('nullable') // Changed from 'required' to support defaults
        ->and($validationRule)->toContain('string')
        ->and($validationRule)->toContain('in:');
    
    // Extract the 'in:' values
    preg_match('/in:([a-z,]+)/', $validationRule, $matches);
    $allowedLanguages = explode(',', $matches[1]);
    
    expect($allowedLanguages)->toEqual($supportedLanguages);
});

test('translation files exist for all supported languages', function () {
    $supportedLanguages = array_keys(config('subscriber_languages.supported'));
    
    foreach ($supportedLanguages as $language) {
        $emailTranslationPath = resource_path("lang/{$language}/emails.php");
        expect(file_exists($emailTranslationPath))->toBeTrue();
        
        // Test that the file contains the required structure
        $translations = include $emailTranslationPath;
        expect($translations)->toBeArray()
            ->and($translations)->toHaveKey('macos_update')
            ->and($translations['macos_update'])->toHaveKey('subject')
            ->and($translations['macos_update'])->toHaveKey('title');
    }
});

test('app locale can be set to supported languages', function () {
    $supportedLanguages = array_keys(config('subscriber_languages.supported'));
    $originalLocale = app()->getLocale();
    
    foreach ($supportedLanguages as $language) {
        app()->setLocale($language);
        expect(app()->getLocale())->toBe($language);
    }
    
    // Reset to original
    app()->setLocale($originalLocale);
});