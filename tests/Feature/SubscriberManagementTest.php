<?php

use App\Models\User;
use App\Models\Subscriber;
use App\Models\SubscriberAction;
use App\Models\Release;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

beforeEach(function () {
    Mail::fake();
});

describe('Subscriber Management', function () {
    it('can create subscriber with admin assignment', function () {
        $admin = User::factory()->create();
        
        $this->actingAs($admin);
        
        $response = $this->post(route('subscribers.store'), [
            'email' => 'test@example.com',
            'subscribed_versions' => ['macOS 14'],
            'days_to_install' => 30
        ]);
        
        $response->assertRedirect()
                ->assertSessionHas('success');
        
        $subscriber = Subscriber::where('email', 'test@example.com')->first();
        expect($subscriber)->not->toBeNull();
        expect($subscriber->admin_id)->toBe($admin->id);
        expect($subscriber->unsubscribe_token)->not->toBeNull();
    });
    
    it('creates subscription action when subscriber is created', function () {
        $admin = User::factory()->create();
        
        $this->actingAs($admin);
        
        $this->post(route('subscribers.store'), [
            'email' => 'test@example.com',
            'subscribed_versions' => ['macOS 14'],
            'days_to_install' => 30
        ]);
        
        $subscriber = Subscriber::where('email', 'test@example.com')->first();
        
        expect($subscriber->actions()->count())->toBe(1);
        expect($subscriber->actions()->first()->action)->toBe('subscribed');
    });
    
    it('can update subscriber subscribed versions', function () {
        $admin = User::factory()->create();
        $subscriber = Subscriber::factory()->create([
            'admin_id' => $admin->id,
            'subscribed_versions' => ['macOS 14']
        ]);
        
        // Create releases for validation
        Release::factory()->create(['major_version' => 'macOS 14']);
        Release::factory()->create(['major_version' => 'macOS 15']);
        
        $this->actingAs($admin);
        
        $response = $this->put(route('subscribers.update', $subscriber), [
            'email' => $subscriber->email,
            'language' => $subscriber->language,
            'subscribed_versions' => ['macOS 14', 'macOS 15'],
            'days_to_install' => $subscriber->days_to_install
        ]);
        
        $response->assertRedirect()
                ->assertSessionHas('success');
        
        expect($subscriber->fresh()->subscribed_versions)->toBe(['macOS 14', 'macOS 15']);
        
        // Check action was logged
        $action = $subscriber->actions()->where('action', 'version_changed')->latest()->first();
        expect($action->action)->toBe('version_changed');
        expect($action->data['old_versions'])->toBe(['macOS 14']);
        expect($action->data['new_versions'])->toBe(['macOS 14', 'macOS 15']);
    });
    
    it('can delete subscriber', function () {
        $admin = User::factory()->create();
        $subscriber = Subscriber::factory()->create(['admin_id' => $admin->id]);
        
        $this->actingAs($admin);
        
        $response = $this->delete(route('subscribers.destroy', $subscriber));
        
        $response->assertRedirect()
                ->assertSessionHas('success');
        
        expect(Subscriber::find($subscriber->id))->toBeNull();
    });
    
    it('prevents admin from accessing other admin subscribers', function () {
        $admin1 = User::factory()->create();
        $admin2 = User::factory()->create();
        
        $subscriber = Subscriber::factory()->create(['admin_id' => $admin2->id]);
        
        $this->actingAs($admin1);
        
        // Cannot view
        $response = $this->get(route('subscribers.show', $subscriber));
        $response->assertForbidden();
        
        // Cannot edit
        $response = $this->get(route('subscribers.edit', $subscriber));
        $response->assertForbidden();
        
        // Cannot update
        $response = $this->put(route('subscribers.update', $subscriber), [
            'email' => $subscriber->email,
            'subscribed_versions' => ['macOS 14'],
            'days_to_install' => 30
        ]);
        $response->assertForbidden();
        
        // Cannot delete
        $response = $this->delete(route('subscribers.destroy', $subscriber));
        $response->assertForbidden();
    });
    
    it('allows super admin to access all subscribers', function () {
        $superAdmin = User::factory()->create(['is_super_admin' => true]);
        $admin = User::factory()->create();
        
        $subscriber = Subscriber::factory()->create(['admin_id' => $admin->id]);
        
        $this->actingAs($superAdmin);
        
        // Can view
        $response = $this->get(route('subscribers.show', $subscriber));
        $response->assertOk();
        
        // Can edit
        $response = $this->get(route('subscribers.edit', $subscriber));
        $response->assertOk();
        
        // Can update
        $response = $this->put(route('subscribers.update', $subscriber), [
            'email' => $subscriber->email,
            'subscribed_versions' => $subscriber->subscribed_versions,
            'days_to_install' => $subscriber->days_to_install
        ]);
        $response->assertRedirect();
    });
});

describe('Public Subscriber Actions', function () {
    it('can unsubscribe with valid token', function () {
        $subscriber = Subscriber::factory()->create([
            'is_subscribed' => true,
            'unsubscribe_token' => Str::random(32)
        ]);
        
        $response = $this->post(route('public.unsubscribe.confirm', $subscriber->unsubscribe_token));
        
        $response->assertRedirect()
                ->assertSessionHas('success');
        
        expect($subscriber->fresh()->is_subscribed)->toBeFalse();
        
        // Check action was logged
        $action = $subscriber->actions()->where('action', 'unsubscribed')->latest()->first();
        expect($action->action)->toBe('unsubscribed');
    });
    
    it('shows unsubscribe form with valid token', function () {
        $subscriber = Subscriber::factory()->create([
            'unsubscribe_token' => Str::random(32)
        ]);
        
        $response = $this->get(route('public.unsubscribe', $subscriber->unsubscribe_token));
        
        $response->assertOk()
                ->assertViewIs('public.unsubscribe')
                ->assertViewHas('subscriber', $subscriber);
    });
    
    it('rejects invalid unsubscribe token', function () {
        $response = $this->get(route('public.unsubscribe', 'invalid-token'));
        
        $response->assertNotFound();
    });
    
    it('can change subscribed versions with valid token', function () {
        $subscriber = Subscriber::factory()->create([
            'subscribed_versions' => ['macOS 14'],
            'unsubscribe_token' => Str::random(32)
        ]);
        
        // Create releases for validation
        Release::factory()->create(['major_version' => 'macOS 14']);
        Release::factory()->create(['major_version' => 'macOS 15']);
        
        $response = $this->post(route('public.version-change.update', $subscriber->unsubscribe_token), [
            'subscribed_versions' => ['macOS 14', 'macOS 15']
        ]);
        
        $response->assertRedirect()
                ->assertSessionHas('success');
        
        expect($subscriber->fresh()->subscribed_versions)->toBe(['macOS 14', 'macOS 15']);
        
        // Check action was logged
        $action = $subscriber->actions()->where('action', 'version_changed')->latest()->first();
        expect($action->action)->toBe('version_changed');
        expect($action->data['old_versions'])->toBe(['macOS 14']);
        expect($action->data['new_versions'])->toBe(['macOS 14', 'macOS 15']);
    });
    
    it('shows version change form with valid token', function () {
        $subscriber = Subscriber::factory()->create([
            'unsubscribe_token' => Str::random(32)
        ]);
        
        $response = $this->get(route('public.version-change', $subscriber->unsubscribe_token));
        
        $response->assertOk()
                ->assertViewIs('public.change-version')
                ->assertViewHas('subscriber', $subscriber);
    });
    
    it('validates subscribed versions in public change', function () {
        $subscriber = Subscriber::factory()->create([
            'unsubscribe_token' => Str::random(32)
        ]);
        
        $response = $this->post(route('public.version-change.update', $subscriber->unsubscribe_token), [
            'subscribed_versions' => ['Invalid Version']
        ]);
        
        $response->assertSessionHasErrors(['subscribed_versions.0']);
    });
});

describe('Bulk Import', function () {
    it('authenticated user can access import page', function () {
        $user = User::factory()->create();
        
        $this->actingAs($user);
        
        $response = $this->get(route('subscribers.import'));
        
        $response->assertOk()
                ->assertViewIs('subscribers.import');
    });
    
    it('guest cannot access import page', function () {
        $response = $this->get(route('subscribers.import'));
        
        $response->assertRedirect(route('magic-link.form'));
    });
    
    it('can access import page with method parameter', function () {
        $user = User::factory()->create();
        
        $this->actingAs($user);
        
        // Test textarea method
        $response = $this->get(route('subscribers.import', ['method' => 'textarea']));
        $response->assertOk()
                ->assertViewIs('subscribers.import')
                ->assertViewHas('method', 'textarea');
        
        // Test CSV method
        $response = $this->get(route('subscribers.import', ['method' => 'csv']));
        $response->assertOk()
                ->assertViewIs('subscribers.import')
                ->assertViewHas('method', 'csv');
    });
    
    it('can import subscribers via textarea', function () {
        $user = User::factory()->create();
        
        $this->actingAs($user);
        
        $emailList = "user1@example.com\nuser2@example.com\nuser3@example.com";
        
        $response = $this->post(route('subscribers.import.process'), [
            'emails' => $emailList,
            'subscribed_versions' => ['macOS 14', 'macOS 15'],
            'days_to_install' => 30
        ]);
        
        $response->assertRedirect(route('subscribers.index'))
                ->assertSessionHas('success');
        
        // Check subscribers were created
        expect(Subscriber::where('email', 'user1@example.com')->first())->not->toBeNull();
        expect(Subscriber::where('email', 'user2@example.com')->first())->not->toBeNull();
        expect(Subscriber::where('email', 'user3@example.com')->first())->not->toBeNull();
        
        // Check they're assigned to the current user
        $subscriber = Subscriber::where('email', 'user1@example.com')->first();
        expect($subscriber->admin_id)->toBe($user->id);
        expect($subscriber->subscribed_versions)->toBe(['macOS 14', 'macOS 15']);
        expect($subscriber->days_to_install)->toBe(30);
        
        // Check import action was logged
        expect($subscriber->actions()->where('action', 'imported')->count())->toBe(1);
        $action = $subscriber->actions()->where('action', 'imported')->first();
        expect($action->data['method'])->toBe('textarea');
        expect($action->data['imported_by'])->toBe($user->email);
    });
    
    it('can import subscribers from CSV file', function () {
        $user = User::factory()->create();
        
        // Create CSV content with emails only
        $csvContent = "email\nuser1@example.com\nuser2@example.com\nuser3@example.com";
        $file = UploadedFile::fake()->createWithContent('subscribers.csv', $csvContent);
        
        $this->actingAs($user);
        
        $response = $this->post(route('subscribers.import.process'), [
            'csv_file' => $file,
            'subscribed_versions' => ['macOS 14'],
            'days_to_install' => 45
        ]);
        
        $response->assertRedirect(route('subscribers.index'))
                ->assertSessionHas('success');
        
        // Check subscribers were created
        expect(Subscriber::where('email', 'user1@example.com')->first())->not->toBeNull();
        expect(Subscriber::where('email', 'user2@example.com')->first())->not->toBeNull();
        expect(Subscriber::where('email', 'user3@example.com')->first())->not->toBeNull();
        
        // Check they're assigned to the current user
        $subscriber = Subscriber::where('email', 'user1@example.com')->first();
        expect($subscriber->admin_id)->toBe($user->id);
        expect($subscriber->subscribed_versions)->toBe(['macOS 14']);
        expect($subscriber->days_to_install)->toBe(45);
        
        // Check import action was logged
        expect($subscriber->actions()->where('action', 'imported')->count())->toBe(1);
        $action = $subscriber->actions()->where('action', 'imported')->first();
        expect($action->data['method'])->toBe('csv');
        expect($action->data['imported_by'])->toBe($user->email);
    });
    
    it('validates textarea import requires fields', function () {
        $user = User::factory()->create();
        
        $this->actingAs($user);
        
        $response = $this->post(route('subscribers.import.process'), [
            'emails' => '', // Empty emails
            'subscribed_versions' => [],
            'days_to_install' => ''
        ]);
        
        $response->assertSessionHasErrors(['emails', 'subscribed_versions', 'days_to_install']);
    });
    
    it('validates CSV import requires fields', function () {
        $user = User::factory()->create();
        
        $this->actingAs($user);
        
        // Test without file or emails
        $response = $this->post(route('subscribers.import.process'), [
            'subscribed_versions' => ['macOS 14'],
            'days_to_install' => 30
        ]);
        
        $response->assertSessionHasErrors(['csv_file']);
        
        // Test with invalid file type
        $file = UploadedFile::fake()->create('document.pdf', 100);
        
        $response = $this->post(route('subscribers.import.process'), [
            'csv_file' => $file,
            'subscribed_versions' => ['macOS 14'],
            'days_to_install' => 30
        ]);
        
        $response->assertSessionHasErrors(['csv_file']);
    });
    
    it('validates subscribed versions exist', function () {
        $user = User::factory()->create();
        
        $this->actingAs($user);
        
        $response = $this->post(route('subscribers.import.process'), [
            'emails' => 'test@example.com',
            'subscribed_versions' => ['Invalid Version'],
            'days_to_install' => 30
        ]);
        
        $response->assertSessionHasErrors(['subscribed_versions']);
    });
    
    it('skips duplicate emails in textarea import', function () {
        $user = User::factory()->create();
        
        // Create existing subscriber
        Subscriber::factory()->create(['email' => 'existing@example.com']);
        
        $this->actingAs($user);
        
        $emailList = "existing@example.com\nnew@example.com";
        
        $response = $this->post(route('subscribers.import.process'), [
            'emails' => $emailList,
            'subscribed_versions' => ['macOS 14'],
            'days_to_install' => 30
        ]);
        
        $response->assertRedirect(route('subscribers.index'))
                ->assertSessionHas('success');
        
        // New subscriber should be created
        expect(Subscriber::where('email', 'new@example.com')->first())->not->toBeNull();
        
        // Should only have one subscriber with existing email
        expect(Subscriber::where('email', 'existing@example.com')->count())->toBe(1);
        
        // Success message should mention skipped
        expect(session('success'))->toContain('1 skipped');
    });
    
    it('skips duplicate emails in CSV import', function () {
        $user = User::factory()->create();
        
        // Create existing subscriber
        Subscriber::factory()->create(['email' => 'existing@example.com']);
        
        $csvContent = "email\nexisting@example.com\nnew@example.com";
        $file = UploadedFile::fake()->createWithContent('subscribers.csv', $csvContent);
        
        $this->actingAs($user);
        
        $response = $this->post(route('subscribers.import.process'), [
            'csv_file' => $file,
            'subscribed_versions' => ['macOS 14'],
            'days_to_install' => 30
        ]);
        
        $response->assertRedirect(route('subscribers.index'))
                ->assertSessionHas('success');
        
        // New subscriber should be created
        expect(Subscriber::where('email', 'new@example.com')->first())->not->toBeNull();
        
        // Should only have one subscriber with existing email
        expect(Subscriber::where('email', 'existing@example.com')->count())->toBe(1);
        
        // Success message should mention skipped
        expect(session('success'))->toContain('1 skipped');
    });
    
    it('handles CSV with header row', function () {
        $user = User::factory()->create();
        
        // CSV with header
        $csvContent = "Email Address,Other Column\nuser1@example.com,data\nuser2@example.com,more data";
        $file = UploadedFile::fake()->createWithContent('subscribers.csv', $csvContent);
        
        $this->actingAs($user);
        
        $response = $this->post(route('subscribers.import.process'), [
            'csv_file' => $file,
            'subscribed_versions' => ['macOS 14'],
            'days_to_install' => 30
        ]);
        
        $response->assertRedirect(route('subscribers.index'))
                ->assertSessionHas('success');
        
        // Check subscribers were created (header should be skipped)
        expect(Subscriber::where('email', 'user1@example.com')->first())->not->toBeNull();
        expect(Subscriber::where('email', 'user2@example.com')->first())->not->toBeNull();
        expect(Subscriber::where('email', 'Email Address')->first())->toBeNull(); // Header should not be imported
    });
    
    it('handles invalid emails gracefully', function () {
        $user = User::factory()->create();
        
        $this->actingAs($user);
        
        $emailList = "valid@example.com\ninvalid-email\nanother@example.com\n@invalid.com";
        
        $response = $this->post(route('subscribers.import.process'), [
            'emails' => $emailList,
            'subscribed_versions' => ['macOS 14'],
            'days_to_install' => 30
        ]);
        
        $response->assertRedirect(route('subscribers.index'))
                ->assertSessionHas('success');
        
        // Valid emails should be imported
        expect(Subscriber::where('email', 'valid@example.com')->first())->not->toBeNull();
        expect(Subscriber::where('email', 'another@example.com')->first())->not->toBeNull();
        
        // Invalid emails should be skipped
        expect(Subscriber::where('email', 'invalid-email')->first())->toBeNull();
        expect(Subscriber::where('email', '@invalid.com')->first())->toBeNull();
        
        // Success message should mention errors
        expect(session('success'))->toContain('Errors:');
    });
    
    it('cleans up email formatting from paste', function () {
        $user = User::factory()->create();
        
        $this->actingAs($user);
        
        // Test various formats that might be pasted (using actual delimiters)
        $emailList = "  user1@example.com  \n\n\nuser2@example.com\nuser3@example.com,user4@example.com;user5@example.com\n  user6@example.com\t";
        
        $response = $this->post(route('subscribers.import.process'), [
            'emails' => $emailList,
            'subscribed_versions' => ['macOS 14'],
            'days_to_install' => 30
        ]);
        
        $response->assertRedirect(route('subscribers.index'))
                ->assertSessionHas('success');
        
        // All valid emails should be imported despite formatting issues
        expect(Subscriber::where('email', 'user1@example.com')->first())->not->toBeNull();
        expect(Subscriber::where('email', 'user2@example.com')->first())->not->toBeNull();
        expect(Subscriber::where('email', 'user3@example.com')->first())->not->toBeNull();
        expect(Subscriber::where('email', 'user4@example.com')->first())->not->toBeNull();
        expect(Subscriber::where('email', 'user5@example.com')->first())->not->toBeNull();
        expect(Subscriber::where('email', 'user6@example.com')->first())->not->toBeNull();
    });
    
    it('enforces days_to_install limits', function () {
        $user = User::factory()->create();
        
        $this->actingAs($user);
        
        // Test minimum
        $response = $this->post(route('subscribers.import.process'), [
            'emails' => 'test@example.com',
            'subscribed_versions' => ['macOS 14'],
            'days_to_install' => 0 // Below minimum
        ]);
        
        $response->assertSessionHasErrors(['days_to_install']);
        
        // Test maximum
        $response = $this->post(route('subscribers.import.process'), [
            'emails' => 'test@example.com',
            'subscribed_versions' => ['macOS 14'],
            'days_to_install' => 366 // Above maximum
        ]);
        
        $response->assertSessionHasErrors(['days_to_install']);
    });
    
    it('can import subscribers with specified language via textarea', function () {
        $user = User::factory()->create();
        
        $this->actingAs($user);
        
        $emailList = "german1@example.com\ngerman2@example.com";
        
        $response = $this->post(route('subscribers.import.process'), [
            'emails' => $emailList,
            'subscribed_versions' => ['macOS 14'],
            'days_to_install' => 30,
            'language' => 'de'
        ]);
        
        $response->assertRedirect(route('subscribers.index'))
                ->assertSessionHas('success');
        
        // Check subscribers were created with correct language
        $subscriber1 = Subscriber::where('email', 'german1@example.com')->first();
        $subscriber2 = Subscriber::where('email', 'german2@example.com')->first();
        
        expect($subscriber1)->not->toBeNull();
        expect($subscriber2)->not->toBeNull();
        expect($subscriber1->language)->toBe('de');
        expect($subscriber2->language)->toBe('de');
        
        // Check import action was logged with language
        $action = $subscriber1->actions()->where('action', 'imported')->first();
        expect($action->data['language'])->toBe('de');
    });
    
    it('can import subscribers with specified language via CSV', function () {
        $user = User::factory()->create();
        
        $csvContent = "email\nfrench1@example.com\nfrench2@example.com";
        $file = UploadedFile::fake()->createWithContent('subscribers.csv', $csvContent);
        
        $this->actingAs($user);
        
        $response = $this->post(route('subscribers.import.process'), [
            'csv_file' => $file,
            'subscribed_versions' => ['macOS 15'],
            'days_to_install' => 45,
            'language' => 'fr'
        ]);
        
        $response->assertRedirect(route('subscribers.index'))
                ->assertSessionHas('success');
        
        // Check subscribers were created with correct language
        $subscriber1 = Subscriber::where('email', 'french1@example.com')->first();
        $subscriber2 = Subscriber::where('email', 'french2@example.com')->first();
        
        expect($subscriber1)->not->toBeNull();
        expect($subscriber2)->not->toBeNull();
        expect($subscriber1->language)->toBe('fr');
        expect($subscriber2->language)->toBe('fr');
        
        // Check import action was logged with language
        $action = $subscriber1->actions()->where('action', 'imported')->first();
        expect($action->data['language'])->toBe('fr');
    });
    
    it('uses default language when no language specified in textarea import', function () {
        $user = User::factory()->create();
        
        $this->actingAs($user);
        
        $emailList = "default@example.com";
        
        $response = $this->post(route('subscribers.import.process'), [
            'emails' => $emailList,
            'subscribed_versions' => ['macOS 14'],
            'days_to_install' => 30
            // No language specified
        ]);
        
        $response->assertRedirect(route('subscribers.index'))
                ->assertSessionHas('success');
        
        // Check subscriber was created with default language
        $subscriber = Subscriber::where('email', 'default@example.com')->first();
        expect($subscriber)->not->toBeNull();
        expect($subscriber->language)->toBe(config('subscriber_languages.default', 'en'));
        
        // Check import action was logged with default language
        $action = $subscriber->actions()->where('action', 'imported')->first();
        expect($action->data['language'])->toBe(config('subscriber_languages.default', 'en'));
    });
    
    it('uses default language when no language specified in CSV import', function () {
        $user = User::factory()->create();
        
        $csvContent = "email\ndefault-csv@example.com";
        $file = UploadedFile::fake()->createWithContent('subscribers.csv', $csvContent);
        
        $this->actingAs($user);
        
        $response = $this->post(route('subscribers.import.process'), [
            'csv_file' => $file,
            'subscribed_versions' => ['macOS 14'],
            'days_to_install' => 30
            // No language specified
        ]);
        
        $response->assertRedirect(route('subscribers.index'))
                ->assertSessionHas('success');
        
        // Check subscriber was created with default language
        $subscriber = Subscriber::where('email', 'default-csv@example.com')->first();
        expect($subscriber)->not->toBeNull();
        expect($subscriber->language)->toBe(config('subscriber_languages.default', 'en'));
        
        // Check import action was logged with default language
        $action = $subscriber->actions()->where('action', 'imported')->first();
        expect($action->data['language'])->toBe(config('subscriber_languages.default', 'en'));
    });
    
    it('validates language parameter in import', function () {
        $user = User::factory()->create();
        
        $this->actingAs($user);
        
        // Test invalid language
        $response = $this->post(route('subscribers.import.process'), [
            'emails' => 'test@example.com',
            'subscribed_versions' => ['macOS 14'],
            'days_to_install' => 30,
            'language' => 'invalid'
        ]);
        
        $response->assertSessionHasErrors(['language']);
    });
    
    it('import page contains supported languages for selection', function () {
        $user = User::factory()->create();
        
        $this->actingAs($user);
        
        $response = $this->get(route('subscribers.import'));
        
        $response->assertOk()
                ->assertViewHas('supportedLanguages')
                ->assertViewIs('subscribers.import');
        
        $supportedLanguages = $response->viewData('supportedLanguages');
        expect($supportedLanguages)->toBeArray();
        expect($supportedLanguages)->toHaveKey('en');
        expect($supportedLanguages)->toHaveKey('de');
        expect($supportedLanguages)->toHaveKey('fr');
        expect($supportedLanguages)->toHaveKey('es');
    });
});
