<?php

use App\Models\User;
use App\Models\Subscriber;
use App\Models\SubscriberAction;
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
            'macos_version' => 'Sonoma',
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
            'macos_version' => 'Sonoma',
            'subscribed_versions' => ['macOS 14'],
            'days_to_install' => 30
        ]);
        
        $subscriber = Subscriber::where('email', 'test@example.com')->first();
        
        expect($subscriber->actions()->count())->toBe(1);
        expect($subscriber->actions()->first()->action)->toBe('subscribed');
    });
    
    it('can update subscriber macOS version', function () {
        $admin = User::factory()->create();
        $subscriber = Subscriber::factory()->create([
            'admin_id' => $admin->id,
            'macos_version' => 'Monterey'
        ]);
        
        $this->actingAs($admin);
        
        $response = $this->put(route('subscribers.update', $subscriber), [
            'email' => $subscriber->email,
            'macos_version' => 'Sonoma',
            'subscribed_versions' => $subscriber->subscribed_versions,
            'days_to_install' => $subscriber->days_to_install
        ]);
        
        $response->assertRedirect()
                ->assertSessionHas('success');
        
        expect($subscriber->fresh()->macos_version)->toBe('Sonoma');
        
        // Check action was logged
        $action = $subscriber->actions()->where('action', 'version_changed')->latest()->first();
        expect($action->action)->toBe('version_changed');
        expect($action->data['old_version'])->toBe('Monterey');
        expect($action->data['new_version'])->toBe('Sonoma');
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
            'macos_version' => 'Sonoma',
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
            'macos_version' => 'Sonoma',
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
    
    it('can change macOS version with valid token', function () {
        $subscriber = Subscriber::factory()->create([
            'macos_version' => 'Monterey',
            'unsubscribe_token' => Str::random(32)
        ]);
        
        $response = $this->post(route('public.version-change.update', $subscriber->unsubscribe_token), [
            'macos_version' => 'Sonoma'
        ]);
        
        $response->assertRedirect()
                ->assertSessionHas('success');
        
        expect($subscriber->fresh()->macos_version)->toBe('Sonoma');
        
        // Check action was logged
        $action = $subscriber->actions()->where('action', 'version_changed')->latest()->first();
        expect($action->action)->toBe('version_changed');
        expect($action->data['old_version'])->toBe('Monterey');
        expect($action->data['new_version'])->toBe('Sonoma');
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
    
    it('validates macOS version in public change', function () {
        $subscriber = Subscriber::factory()->create([
            'unsubscribe_token' => Str::random(32)
        ]);
        
        $response = $this->post(route('public.version-change.update', $subscriber->unsubscribe_token), [
            'macos_version' => 'Invalid Version'
        ]);
        
        $response->assertSessionHasErrors(['macos_version']);
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
});
