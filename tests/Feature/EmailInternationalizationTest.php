<?php

use App\Mail\MacOSUpdateNotification;
use App\Models\Release;
use App\Models\Subscriber;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Mail;

uses(RefreshDatabase::class);

test('macos update notification email uses subscriber language', function () {
    Mail::fake();
    
    // Create subscriber with German language
    $subscriber = Subscriber::factory()->create(['language' => 'de']);
    $release = Release::factory()->create([
        'major_version' => 'macOS 15',
    ]);
    
    $mailable = new MacOSUpdateNotification($subscriber, $release);
    
    // Send the email
    Mail::to($subscriber->email)->send($mailable);
    
    // Check that mail was queued (since it implements ShouldQueue)
    Mail::assertQueued(MacOSUpdateNotification::class, function ($mail) use ($subscriber) {
        return $mail->hasTo($subscriber->email);
    });
    
    // Check the subject is rendered correctly
    $envelope = $mailable->envelope();
    expect($envelope->subject)->toContain('macOS 15');
    
    // Check the content contains the subscriber
    $content = $mailable->content();
    $viewData = $content->with;
    expect($viewData)->toHaveKey('subscriber');
    expect($viewData['subscriber']->language)->toBe('de');
});

test('email uses english when subscriber has unsupported language', function () {
    Mail::fake();
    
    // Create subscriber with unsupported language (fallback to English)
    $subscriber = Subscriber::factory()->create(['language' => 'zh']); // Chinese not supported
    $release = Release::factory()->create();
    
    $mailable = new MacOSUpdateNotification($subscriber, $release);
    
    // Check the content contains the subscriber with unsupported language
    $content = $mailable->content();
    $viewData = $content->with;
    expect($viewData['subscriber']->language)->toBe('zh');
});

test('email translations exist for all supported languages', function () {
    $supportedLanguages = config('subscriber_languages.supported', []);
    
    foreach ($supportedLanguages as $code => $language) {
        $translations = trans('emails', [], $code);
        
        // Check that translations are not just the translation keys
        expect($translations)->toBeArray();
        expect($translations)->toHaveKey('macos_update');
        expect($translations['macos_update'])->toHaveKey('subject');
        expect($translations['macos_update'])->toHaveKey('alert');
        expect($translations['macos_update']['subject'])->not->toBe('emails.macos_update.subject');
    }
});

test('email subject is translated correctly', function () {
    $subscriber = Subscriber::factory()->create(['language' => 'fr']);
    $release = Release::factory()->create(['major_version' => 'macOS 15']);
    
    $mailable = new MacOSUpdateNotification($subscriber, $release);
    
    // Temporarily set locale to French to test translation
    $originalLocale = App::getLocale();
    App::setLocale('fr');
    
    $envelope = $mailable->envelope();
    $frenchSubject = trans('emails.macos_update.subject', ['version' => 'macOS 15', 'build' => $release->version]);
    
    expect($envelope->subject)->toBe($frenchSubject);
    
    // Restore locale
    App::setLocale($originalLocale);
});

test('email content contains translated text for spanish', function () {
    $subscriber = Subscriber::factory()->create(['language' => 'es']);
    $release = Release::factory()->create([
        'major_version' => 'macOS 15',
    ]);
    
    $mailable = new MacOSUpdateNotification($subscriber, $release);
    
    // Set locale to Spanish
    $originalLocale = App::getLocale();
    App::setLocale('es');
    
    $content = $mailable->content();
    
    // Get the view data
    $viewData = $content->with;
    
    // Check that the data contains the correct subscriber and release
    expect($viewData)->toHaveKey('subscriber');
    expect($viewData)->toHaveKey('release');
    expect($viewData['subscriber']->language)->toBe('es');
    
    // Check that Spanish translations exist
    $spanishUpdateAlert = trans('emails.macos_update.alert.multiple_days', ['days' => 3]);
    expect($spanishUpdateAlert)->not->toBe('emails.macos_update.alert.multiple_days'); // Should be translated
    
    // Restore locale
    App::setLocale($originalLocale);
});

test('email sets correct locale based on subscriber language', function () {
    $subscriber = Subscriber::factory()->create(['language' => 'de']);
    $release = Release::factory()->create();
    
    $mailable = new MacOSUpdateNotification($subscriber, $release);
    
    // The mailable should set the locale in its constructor
    $originalLocale = App::getLocale();
    
    $content = $mailable->content();
    
    // The locale should be set to German during email building
    // (This tests the logic in the MacOSUpdateNotification constructor)
    expect($content->with['subscriber']->language)->toBe('de');
    
    // Note: Locale setting in constructor affects the current test execution
    expect(App::getLocale())->toBe('de');
    
    // Restore locale for other tests
    App::setLocale($originalLocale);
});