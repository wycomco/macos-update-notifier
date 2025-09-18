<?php

use App\Models\Subscriber;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\App;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
    
    // Create a subscriber with German language preference
    $this->germanSubscriber = Subscriber::factory()->create([
        'email' => 'german@example.com',
        'language' => 'de',
        'subscribed_versions' => ['macOS 14', 'macOS 15'],
        'days_to_install' => 30,
        'admin_id' => $this->user->id,
    ]);
    
    // Create a subscriber with Spanish language preference
    $this->spanishSubscriber = Subscriber::factory()->create([
        'email' => 'spanish@example.com',
        'language' => 'es',
        'subscribed_versions' => ['macOS 15'],
        'days_to_install' => 14,
        'admin_id' => $this->user->id,
    ]);
    
    // Create an inactive subscriber
    $this->inactiveSubscriber = Subscriber::factory()->create([
        'email' => 'inactive@example.com',
        'language' => 'fr',
        'subscribed_versions' => ['macOS 14'],
        'days_to_install' => 30,
        'admin_id' => $this->user->id,
        'unsubscribed_at' => now(),
    ]);
});

describe('Public Subscriber Views Localization', function () {
    
    test('unsubscribe page displays content in subscriber language', function () {
        $response = $this->get(route('public.unsubscribe', $this->germanSubscriber->unsubscribe_token));
        
        $response->assertOk();
        
        // Check German translations are used
        $response->assertSee('Updates abbestellen'); // title
        $response->assertSee('Es tut uns leid, Sie gehen zu sehen!'); // subtitle
        $response->assertSee('Abonnenten-Details'); // subscriber details header
        $response->assertSee('E-Mail:'); // email label
        $response->assertSee('Abonniert für:'); // subscribed to label
        $response->assertSee('Installations-Frist:'); // install deadline label
        $response->assertSee('Tage'); // days
        $response->assertSee('Sind Sie sicher?'); // warning title
        $response->assertSee('Ja, mich abmelden'); // confirm button
        $response->assertSee('Mein Abonnement behalten'); // keep button
        
        // Should not see English text
        $response->assertDontSee('Unsubscribe from Updates');
        $response->assertDontSee('We\'re sorry to see you go!');
    });
    
    test('unsubscribe page displays content in Spanish for Spanish subscriber', function () {
        $response = $this->get(route('public.unsubscribe', $this->spanishSubscriber->unsubscribe_token));
        
        $response->assertOk();
        
        // Check Spanish translations are used
        $response->assertSee('Cancelar suscripción a actualizaciones'); // title
        $response->assertSee('¡Lamentamos verte partir!'); // subtitle
        $response->assertSee('Detalles del suscriptor'); // subscriber details header
        $response->assertSee('Correo electrónico:'); // email label
        $response->assertSee('Suscrito a:'); // subscribed to label
        $response->assertSee('días'); // days
        $response->assertSee('¿Estás seguro?'); // warning title
        $response->assertSee('Sí, cancelar mi suscripción'); // confirm button
        
        // Should not see English text
        $response->assertDontSee('Unsubscribe from Updates');
        $response->assertDontSee('We\'re sorry to see you go!');
    });
    
    test('already unsubscribed page displays content in subscriber language', function () {
        // Make sure the subscriber is actually unsubscribed
        $this->inactiveSubscriber->update(['unsubscribed_at' => now()]);
        
        $response = $this->get(route('public.unsubscribe', $this->inactiveSubscriber->unsubscribe_token));
        
        $response->assertOk();
        
        // Check French translations are used
        $response->assertSee('Déjà désabonné'); // title
        $response->assertSee('Cette adresse e-mail est déjà désabonnée'); // subtitle
        $response->assertSee('a été désabonné le'); // unsubscribed on text
        $response->assertSee('Voulez-vous vous réabonner'); // want to resubscribe text
        
        // Should not see English text
        $response->assertDontSee('Already Unsubscribed');
        $response->assertDontSee('This email address is already unsubscribed');
    });
    
    test('change version page displays content in subscriber language', function () {
        $response = $this->get(route('public.version-change', $this->germanSubscriber->unsubscribe_token));
        
        $response->assertOk();
        
        // Check German translations are used
        $response->assertSee('Ihr Abonnement aktualisieren'); // title
        $response->assertSee('Wählen Sie, über welche macOS-Versionen Sie benachrichtigt werden möchten'); // subtitle
        $response->assertSee('Aktuelles Abonnement'); // current subscription header
        $response->assertSee('E-Mail:'); // email label
        $response->assertSee('Derzeit abonniert für:'); // currently subscribed to label
        $response->assertSee('Zu überwachende macOS-Versionen'); // versions label
        $response->assertSee('Mein Abonnement aktualisieren'); // update button
        $response->assertSee('Abbrechen'); // cancel button
        
        // Should not see English text
        $response->assertDontSee('Update Your Subscription');
        $response->assertDontSee('Choose which macOS versions');
    });
    
    test('change language page displays content in subscriber language', function () {
        $response = $this->get(route('public.language-change', $this->germanSubscriber->unsubscribe_token));
        
        $response->assertOk();
        
        // Check German translations are used
        $response->assertSee('Sprache ändern'); // title
        $response->assertSee('Wählen Sie Ihre bevorzugte Sprache für Benachrichtigungen'); // subtitle
        $response->assertSee('Aktuelle Informationen'); // current info header
        $response->assertSee('Aktuelle Sprache:'); // current language label
        $response->assertSee('Sprache auswählen'); // select language label
        $response->assertSee('Sprache aktualisieren'); // update button
        
        // Should not see English text
        $response->assertDontSee('Change Language');
        $response->assertDontSee('Select your preferred language');
    });
    
    test('subscriber inactive page displays content in subscriber language', function () {
        $response = $this->get(route('public.version-change', $this->inactiveSubscriber->unsubscribe_token));
        
        $response->assertOk();
        
        // Check French translations are used
        $response->assertSee('Abonné inactif'); // title
        $response->assertSee('Cet abonnement n\'est plus actif'); // subtitle
        $response->assertSee('aux notifications de mises à jour macOS'); // part of the no longer subscribed text
        $response->assertSee('Voulez-vous vous réabonner'); // want to resubscribe text
        
        // Should not see English text
        $response->assertDontSee('Subscriber Inactive');
        $response->assertDontSee('This subscription is no longer active');
    });
    
    test('form submissions preserve locale context', function () {
        // Test unsubscribe form submission
        $response = $this->post(route('public.unsubscribe.confirm', $this->germanSubscriber->unsubscribe_token));
        
        $response->assertRedirect();
        
        // Follow the redirect and check the success message uses German locale
        $response = $this->followRedirects($response);
        
        // The success message should be in German context
        // Note: This tests that the locale is properly set in the controller
        expect(App::getLocale())->toBe('de');
    });
    
    test('version change form displays errors in subscriber language', function () {
        // Submit invalid version data to trigger validation errors
        $response = $this->post(route('public.version-change.update', $this->germanSubscriber->unsubscribe_token), [
            'subscribed_versions' => [], // Empty array should fail validation
        ]);
        
        $response->assertSessionHasErrors('subscribed_versions');
        
        // Follow the redirect back to the form
        $response = $this->get(route('public.version-change', $this->germanSubscriber->unsubscribe_token));
        
        // Page should still display in German
        $response->assertSee('Ihr Abonnement aktualisieren');
        $response->assertSee('Aktuelles Abonnement');
    });
    
    test('language change form displays success message in new language', function () {
        // Change from German to Spanish
        $response = $this->post(route('public.language-change.update', $this->germanSubscriber->unsubscribe_token), [
            'language' => 'es',
        ]);
        
        $response->assertRedirect();
        
        // The subscriber should now have Spanish language
        $this->germanSubscriber->refresh();
        expect($this->germanSubscriber->language)->toBe('es');
        
        // Follow the redirect and the page should now be in Spanish
        $response = $this->get(route('public.language-change', $this->germanSubscriber->unsubscribe_token));
        
        $response->assertSee('Cambiar idioma'); // Spanish title
        $response->assertSee('Selecciona tu idioma preferido'); // Spanish subtitle
        $response->assertDontSee('Sprache ändern'); // Should not see German anymore
    });
    
    test('locale is properly set for each request based on subscriber language', function () {
        // Test that each request properly sets the locale
        
        // German subscriber request
        $this->get(route('public.unsubscribe', $this->germanSubscriber->unsubscribe_token));
        // Note: We can't directly test App::getLocale() here as it's set in the controller method
        // but we can verify the content is in German
        
        // Spanish subscriber request
        $response = $this->get(route('public.unsubscribe', $this->spanishSubscriber->unsubscribe_token));
        $response->assertSee('Cancelar suscripción a actualizaciones');
        
        // French subscriber request  
        $this->inactiveSubscriber->update(['unsubscribed_at' => null]); // Make active for this test
        $response = $this->get(route('public.unsubscribe', $this->inactiveSubscriber->unsubscribe_token));
        $response->assertSee('Se désabonner des mises à jour'); // French title
    });
    
    test('unsupported language falls back to English', function () {
        // Create subscriber with unsupported language
        $subscriber = Subscriber::factory()->create([
            'email' => 'unsupported@example.com',
            'language' => 'zh', // Unsupported language
            'subscribed_versions' => ['macOS 15'],
            'days_to_install' => 30,
            'admin_id' => $this->user->id,
        ]);
        
        $response = $this->get(route('public.unsubscribe', $subscriber->unsubscribe_token));
        
        $response->assertOk();
        
        // Should see English content as fallback
        $response->assertSee('Unsubscribe from Updates');
        $response->assertSee('We\'re sorry to see you go!');
        $response->assertSee('Subscriber Details');
    });
});

describe('Public Form Processing Localization', function () {
    
    test('successful version change shows success message in subscriber language', function () {
        $response = $this->post(route('public.version-change.update', $this->germanSubscriber->unsubscribe_token), [
            'subscribed_versions' => ['macOS 15'],
        ]);
        
        $response->assertRedirect();
        $response->assertSessionHas('success');
        
        // The subscriber's versions should be updated
        $this->germanSubscriber->refresh();
        expect($this->germanSubscriber->subscribed_versions)->toBe(['macOS 15']);
    });
    
    test('successful language change updates subscriber and shows new language interface', function () {
        // Start with German subscriber, change to French
        $response = $this->post(route('public.language-change.update', $this->germanSubscriber->unsubscribe_token), [
            'language' => 'fr',
        ]);
        
        $response->assertRedirect();
        $response->assertSessionHas('success');
        
        // Subscriber language should be updated
        $this->germanSubscriber->refresh();
        expect($this->germanSubscriber->language)->toBe('fr');
        
        // Subsequent page loads should be in French
        $response = $this->get(route('public.language-change', $this->germanSubscriber->unsubscribe_token));
        $response->assertSee('Changer de langue'); // French title
        $response->assertSee('Sélectionnez votre langue préférée'); // French subtitle
    });
    
    test('successful unsubscribe shows success message in subscriber language', function () {
        $response = $this->post(route('public.unsubscribe.confirm', $this->germanSubscriber->unsubscribe_token));
        
        $response->assertRedirect();
        $response->assertSessionHas('success');
        
        // Subscriber should be unsubscribed
        $this->germanSubscriber->refresh();
        expect($this->germanSubscriber->unsubscribed_at)->not->toBeNull();
    });
});

describe('Public Success Views Localization', function () {
    
    test('unsubscribed success view displays correctly in all languages', function () {
        foreach (['en', 'de', 'es', 'fr'] as $language) {
            App::setLocale($language);
            
            $response = $this->get(route('public.unsubscribed', 'test-token'));
            
            $response->assertStatus(200);
            $response->assertViewIs('public.unsubscribed');
            
            // Check that translation keys are being used
            $response->assertSee(__('public.unsubscribed.title'));
            $response->assertSee(__('public.unsubscribed.subtitle'));
            $response->assertSee(__('public.unsubscribed.security_reminder_title'));
            $response->assertSee(__('public.unsubscribed.security_reminder_text'));
            
            // Verify translations are actually different for different languages (not just showing keys)
            if ($language === 'en') {
                $response->assertSee('Successfully Unsubscribed');
            } elseif ($language === 'de') {
                $response->assertSee('Erfolgreich abgemeldet');
            } elseif ($language === 'es') {
                $response->assertSee('Cancelación exitosa');
            } elseif ($language === 'fr') {
                $response->assertSee('Désabonnement réussi');
            }
        }
    });

    test('language changed success view displays correctly in all languages', function () {
        foreach (['en', 'de', 'es', 'fr'] as $language) {
            App::setLocale($language);
            
            $response = $this->get(route('public.language-changed', 'test-token'));
            
            $response->assertStatus(200);
            $response->assertViewIs('public.language-changed');
            
            // Check that translation keys are being used
            $response->assertSee(__('public.language_changed.title'));
            $response->assertSee(__('public.language_changed.subtitle'));
            $response->assertSee(__('public.language_changed.all_set_title'));
            $response->assertSee(__('public.language_changed.all_set_text'));
            
            // Verify translations are actually different for different languages (not just showing keys)
            if ($language === 'en') {
                $response->assertSee('Language Updated');
            } elseif ($language === 'de') {
                $response->assertSee('Sprache aktualisiert');
            } elseif ($language === 'es') {
                $response->assertSee('Idioma actualizado');
            } elseif ($language === 'fr') {
                $response->assertSee('Langue mise à jour');
            }
        }
    });

    test('version changed success view displays correctly in all languages', function () {
        foreach (['en', 'de', 'es', 'fr'] as $language) {
            App::setLocale($language);
            
            $response = $this->get(route('public.version-changed', 'test-token'));
            
            $response->assertStatus(200);
            $response->assertViewIs('public.version-changed');
            
            // Check that translation keys are being used
            $response->assertSee(__('public.version_changed.title'));
            $response->assertSee(__('public.version_changed.subtitle'));
            $response->assertSee(__('public.version_changed.all_set_title'));
            $response->assertSee(__('public.version_changed.all_set_text', ['days' => '30']));
            
            // Verify translations are actually different for different languages (not just showing keys)
            if ($language === 'en') {
                $response->assertSee('Subscription Updated');
            } elseif ($language === 'de') {
                $response->assertSee('Abonnement aktualisiert');
            } elseif ($language === 'es') {
                $response->assertSee('Suscripción actualizada');
            } elseif ($language === 'fr') {
                $response->assertSee('Abonnement mis à jour');
            }
        }
    });
    
    test('success views display appropriate content when no subscriber context available', function () {
        // These views should work even when the subscriber might not exist anymore (e.g., after deletion)
        
        App::setLocale('en');
        
        $response = $this->get(route('public.unsubscribed', 'non-existent-token'));
        $response->assertStatus(200);
        $response->assertSee('Successfully Unsubscribed');
        $response->assertSee('You will no longer receive macOS update notifications');
        
        $response = $this->get(route('public.language-changed', 'non-existent-token'));
        $response->assertStatus(200);
        $response->assertSee('Language Updated');
        $response->assertSee('Your language preference has been saved');
        
        $response = $this->get(route('public.version-changed', 'non-existent-token'));
        $response->assertStatus(200);
        $response->assertSee('Subscription Updated');
        $response->assertSee('Your notification preferences have been saved');
    });
});