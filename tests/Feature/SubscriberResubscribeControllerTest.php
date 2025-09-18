<?php

use App\Models\Subscriber;
use App\Models\SubscriberAction;
use App\Models\User;

beforeEach(function () {
    $this->superAdmin = User::factory()->create(['is_super_admin' => true]);
    $this->admin = User::factory()->create(['is_super_admin' => false]);
    $this->otherAdmin = User::factory()->create(['is_super_admin' => false]);
    
    $this->subscriber = Subscriber::factory()->create([
        'email' => 'test@example.com',
        'is_subscribed' => false,
        'unsubscribed_at' => now()->subDays(5),
        'admin_id' => $this->admin->id,
    ]);

    $this->otherSubscriber = Subscriber::factory()->create([
        'email' => 'other@example.com',
        'is_subscribed' => false,
        'unsubscribed_at' => now()->subDays(3),
        'admin_id' => $this->otherAdmin->id,
    ]);
});

// Model resubscribe functionality tests
test('subscriber model returns true for active user with no unsubscribe date', function () {
    $subscriber = Subscriber::factory()->create([
        'is_subscribed' => true,
        'unsubscribed_at' => null,
    ]);

    expect($subscriber->isActive())->toBeTrue();
});

test('subscriber model returns false for unsubscribed user', function () {
    $subscriber = Subscriber::factory()->create([
        'is_subscribed' => false,
        'unsubscribed_at' => now()->subDays(1),
    ]);

    expect($subscriber->isActive())->toBeFalse();
});

test('subscriber model returns false for user with unsubscribe date even if is_subscribed is true', function () {
    $subscriber = Subscriber::factory()->create([
        'is_subscribed' => true,
        'unsubscribed_at' => now()->subDays(1),
    ]);

    expect($subscriber->isActive())->toBeFalse();
});

test('subscriber model can resubscribe an unsubscribed subscriber', function () {
    $admin = User::factory()->create();
    $subscriber = Subscriber::factory()->create([
        'email' => 'unique-test@example.com',
        'is_subscribed' => false,
        'unsubscribed_at' => now()->subDays(5),
        'admin_id' => $admin->id,
    ]);

    // Ensure subscriber is unsubscribed
    expect($subscriber->isActive())->toBeFalse();
    expect($subscriber->unsubscribed_at)->not()->toBeNull();
    expect($subscriber->is_subscribed)->toBeFalse();

    // Resubscribe the subscriber
    $subscriber->resubscribe($admin, 'email', 'Test resubscribe');

    // Refresh the model from database
    $subscriber->refresh();

    // Verify the subscriber is now active
    expect($subscriber->isActive())->toBeTrue();
    expect($subscriber->unsubscribed_at)->toBeNull();
    expect($subscriber->is_subscribed)->toBeTrue();
});

test('subscriber model logs resubscribe action when resubscribing', function () {
    $admin = User::factory()->create();
    $subscriber = Subscriber::factory()->create(['unsubscribed_at' => now()]);
    
    $subscriber->resubscribe($admin, 'email', 'Test resubscribe notes');
    
    $action = SubscriberAction::latest()->first();

    expect($action->subscriber_id)->toBe($subscriber->id);
    expect($action->action)->toBe('resubscribed');
    expect($action->data['admin_id'])->toBe($admin->id);
    expect($action->data['admin_email'])->toContain($admin->email);
    expect($action->data['consent_notes'])->toContain('Test resubscribe notes');
});

test('subscriber model stores admin information in action details', function () {
    $admin = User::factory()->create([
        'name' => 'Test Admin',
        'email' => 'admin@test.com'
    ]);
    $subscriber = Subscriber::factory()->create(['unsubscribed_at' => now()]);
    
    $subscriber->resubscribe($admin, 'phone', 'Phone call confirmation');
    
    $action = SubscriberAction::latest()->first();
    $details = $action->data;

    expect($details['admin_name'])->toBe($admin->name);
    expect($details['admin_email'])->toBe($admin->email);
    expect($details['consent_method'])->toBe('phone');
    expect($details['consent_notes'])->toBe('Phone call confirmation');
    expect($details['resubscribed_at'])->not()->toBeNull();
});

test('subscriber model can resubscribe a subscriber that was never unsubscribed', function () {
    $admin = User::factory()->create();
    $activeSubscriber = Subscriber::factory()->create([
        'is_subscribed' => true,
        'unsubscribed_at' => null,
        'admin_id' => $admin->id,
    ]);

    expect($activeSubscriber->isActive())->toBeTrue();

    // Resubscribe should work (idempotent operation)
    $activeSubscriber->resubscribe($admin, 'website_form', 'Double subscription request');

    $activeSubscriber->refresh();

    // Should still be active
    expect($activeSubscriber->isActive())->toBeTrue();
    expect($activeSubscriber->unsubscribed_at)->toBeNull();
    expect($activeSubscriber->is_subscribed)->toBeTrue();

    // Should still log the action
    $action = SubscriberAction::where('subscriber_id', $activeSubscriber->id)
        ->where('action', 'resubscribed')
        ->latest()
        ->first();

    expect($action)->not()->toBeNull();
});

test('subscriber model handles null consent notes gracefully', function () {
    $admin = User::factory()->create();
    $subscriber = Subscriber::factory()->create([
        'is_subscribed' => false,
        'unsubscribed_at' => now()->subDays(5),
        'admin_id' => $admin->id,
    ]);

    $subscriber->resubscribe($admin, 'in_person', null);

    $subscriber->refresh();
    expect($subscriber->isActive())->toBeTrue();

    $action = SubscriberAction::latest()->first();
    $details = $action->data;

    expect($details['consent_notes'])->toBeNull();
});

test('subscriber model validates different consent methods', function () {
    $admin = User::factory()->create();
    $consentMethods = [
        'email',
        'phone',
        'in_person',
        'support_ticket',
        'written_form',
        'website_form',
        'other'
    ];

    foreach ($consentMethods as $method) {
        $subscriber = Subscriber::factory()->create([
            'is_subscribed' => false,
            'unsubscribed_at' => now()->subDays(1),
            'admin_id' => $admin->id,
        ]);

        $subscriber->resubscribe($admin, $method, "Test for method: {$method}");

        $subscriber->refresh();
        expect($subscriber->isActive())->toBeTrue();

        $action = SubscriberAction::where('subscriber_id', $subscriber->id)
            ->where('action', 'resubscribed')
            ->latest()
            ->first();

        $details = $action->data;
        expect($details['consent_method'])->toBe($method);
    }
});

// Controller tests
test('super admin can resubscribe any subscriber', function () {
    $response = $this->actingAs($this->superAdmin)
        ->post(route('subscribers.resubscribe', $this->subscriber), [
            'consent_method' => 'email',
            'consent_notes' => 'Email confirmation received',
            'legal_confirmation' => '1',
        ]);

    $response->assertRedirect(route('subscribers.show', $this->subscriber));
    $response->assertSessionHas('success');

    $this->subscriber->refresh();
    expect($this->subscriber->isActive())->toBeTrue();
});

test('admin can resubscribe their own subscriber', function () {
    $response = $this->actingAs($this->admin)
        ->post(route('subscribers.resubscribe', $this->subscriber), [
            'consent_method' => 'phone',
            'consent_notes' => 'Phone call confirmation',
            'legal_confirmation' => '1',
        ]);

    $response->assertRedirect(route('subscribers.show', $this->subscriber));
    $response->assertSessionHas('success');

    $this->subscriber->refresh();
    expect($this->subscriber->isActive())->toBeTrue();
});

test('admin cannot resubscribe another admins subscriber', function () {
    $response = $this->actingAs($this->admin)
        ->post(route('subscribers.resubscribe', $this->otherSubscriber), [
            'consent_method' => 'email',
            'consent_notes' => 'Test',
            'legal_confirmation' => '1',
        ]);

    $response->assertStatus(403);

    $this->otherSubscriber->refresh();
    expect($this->otherSubscriber->isActive())->toBeFalse();
});

test('resubscribe requires authentication', function () {
    $response = $this->post(route('subscribers.resubscribe', $this->subscriber), [
        'consent_method' => 'email',
        'consent_notes' => 'Test',
        'legal_confirmation' => '1',
    ]);

    $response->assertRedirect(route('magic-link.form'));

    $this->subscriber->refresh();
    expect($this->subscriber->isActive())->toBeFalse();
});

test('resubscribe validates required consent_method field', function () {
    $response = $this->actingAs($this->admin)
        ->post(route('subscribers.resubscribe', $this->subscriber), [
            'consent_notes' => 'Some notes',
            'legal_confirmation' => '1',
        ]);

    $response->assertSessionHasErrors('consent_method');

    $this->subscriber->refresh();
    expect($this->subscriber->isActive())->toBeFalse();
});

test('resubscribe validates required legal_confirmation field', function () {
    $response = $this->actingAs($this->admin)
        ->post(route('subscribers.resubscribe', $this->subscriber), [
            'consent_method' => 'email',
            'consent_notes' => 'Some notes',
        ]);

    $response->assertSessionHasErrors('legal_confirmation');

    $this->subscriber->refresh();
    expect($this->subscriber->isActive())->toBeFalse();
});

test('resubscribe validates consent_method is in allowed values', function () {
    $response = $this->actingAs($this->admin)
        ->post(route('subscribers.resubscribe', $this->subscriber), [
            'consent_method' => 'invalid_method',
            'consent_notes' => 'Some notes',
            'legal_confirmation' => '1',
        ]);

    $response->assertSessionHasErrors('consent_method');

    $this->subscriber->refresh();
    expect($this->subscriber->isActive())->toBeFalse();
});

test('resubscribe allows all valid consent methods', function () {
    $validMethods = [
        'email',
        'phone', 
        'in_person',
        'support_ticket',
        'written_form',
        'website_form',
        'other'
    ];

    foreach ($validMethods as $method) {
        // Create a fresh unsubscribed subscriber for each test
        $subscriber = Subscriber::factory()->create([
            'is_subscribed' => false,
            'unsubscribed_at' => now()->subDays(1),
            'admin_id' => $this->admin->id,
        ]);

        $response = $this->actingAs($this->admin)
            ->post(route('subscribers.resubscribe', $subscriber), [
                'consent_method' => $method,
                'consent_notes' => "Test for {$method}",
                'legal_confirmation' => '1',
            ]);

        $response->assertRedirect(route('subscribers.show', $subscriber));
        
        $subscriber->refresh();
        expect($subscriber->isActive())->toBeTrue();
    }
});

test('resubscribe handles optional consent_notes field', function () {
    $response = $this->actingAs($this->admin)
        ->post(route('subscribers.resubscribe', $this->subscriber), [
            'consent_method' => 'email',
            'legal_confirmation' => '1',
        ]);

    $response->assertRedirect(route('subscribers.show', $this->subscriber));
    $response->assertSessionHas('success');

    $this->subscriber->refresh();
    expect($this->subscriber->isActive())->toBeTrue();
});

test('resubscribe logs additional consent information in subscriber action', function () {
    $consentNotes = 'Customer called support line and specifically requested re-enabling subscription. Ticket #12345.';
    
    $this->actingAs($this->admin)
        ->post(route('subscribers.resubscribe', $this->subscriber), [
            'consent_method' => 'support_ticket',
            'consent_notes' => $consentNotes,
            'legal_confirmation' => '1',
        ]);

    // Get the latest subscriber action
    $action = SubscriberAction::where('subscriber_id', $this->subscriber->id)
        ->where('action', 'resubscribed')
        ->latest()
        ->first();

    expect($action)->not()->toBeNull();

    $details = $action->data;
    expect($details['consent_method'])->toBe('support_ticket');
    expect($details['consent_notes'])->toBe($consentNotes);
    expect($details['admin_name'])->toBe($this->admin->name);
    expect($details['admin_email'])->toBe($this->admin->email);
});

test('can resubscribe an already active subscriber', function () {
    // First resubscribe the subscriber
    $this->subscriber->resubscribe($this->admin, 'email', 'Initial resubscribe');
    $this->subscriber->refresh();
    
    expect($this->subscriber->isActive())->toBeTrue();

    // Try to resubscribe again
    $response = $this->actingAs($this->admin)
        ->post(route('subscribers.resubscribe', $this->subscriber), [
            'consent_method' => 'phone',
            'consent_notes' => 'Double confirmation request',
            'legal_confirmation' => '1',
        ]);

    $response->assertRedirect(route('subscribers.show', $this->subscriber));
    $response->assertSessionHas('success');

    $this->subscriber->refresh();
    expect($this->subscriber->isActive())->toBeTrue();

    // Should have two resubscribe actions
    $actions = SubscriberAction::where('subscriber_id', $this->subscriber->id)
        ->where('action', 'resubscribed')
        ->count();

    expect($actions)->toBe(2);
});

test('resubscribe shows appropriate success message', function () {
    $response = $this->actingAs($this->admin)
        ->post(route('subscribers.resubscribe', $this->subscriber), [
            'consent_method' => 'email',
            'consent_notes' => 'Email confirmation',
            'legal_confirmation' => '1',
        ]);

    $response->assertSessionHas('success', 'Subscription has been successfully re-enabled.');
});

test('resubscribe returns 404 for non-existent subscriber', function () {
    $response = $this->actingAs($this->admin)
        ->post(route('subscribers.resubscribe', 99999), [
            'consent_method' => 'email',
            'consent_notes' => 'Test',
            'legal_confirmation' => '1',
        ]);

    $response->assertStatus(404);
});

test('resubscribe validates legal_confirmation must be accepted', function () {
    $response = $this->actingAs($this->admin)
        ->post(route('subscribers.resubscribe', $this->subscriber), [
            'consent_method' => 'email',
            'consent_notes' => 'Test',
            'legal_confirmation' => '0',
        ]);

    $response->assertSessionHasErrors('legal_confirmation');

    $this->subscriber->refresh();
    expect($this->subscriber->isActive())->toBeFalse();
});

test('resubscribe limits consent_notes to reasonable length', function () {
    $longNotes = str_repeat('This is a very long note. ', 100); // Very long string

    $response = $this->actingAs($this->admin)
        ->post(route('subscribers.resubscribe', $this->subscriber), [
            'consent_method' => 'email',
            'consent_notes' => $longNotes,
            'legal_confirmation' => '1',
        ]);

    $response->assertSessionHasErrors('consent_notes');

    $this->subscriber->refresh();
    expect($this->subscriber->isActive())->toBeFalse();
});

test('super admin can view any subscriber', function () {
    $response = $this->actingAs($this->superAdmin)
        ->get(route('subscribers.show', $this->otherSubscriber));

    $response->assertOk();
    $response->assertViewIs('subscribers.show');
    $response->assertViewHas('subscriber', $this->otherSubscriber);
});

test('admin can view their own subscriber', function () {
    $response = $this->actingAs($this->admin)
        ->get(route('subscribers.show', $this->subscriber));

    $response->assertOk();
    $response->assertViewIs('subscribers.show');
    $response->assertViewHas('subscriber', $this->subscriber);
});

test('admin cannot view another admins subscriber', function () {
    $response = $this->actingAs($this->admin)
        ->get(route('subscribers.show', $this->otherSubscriber));

    $response->assertStatus(403);
});

test('show requires authentication', function () {
    $response = $this->get(route('subscribers.show', $this->subscriber));

    $response->assertRedirect(route('magic-link.form'));
});

test('resubscribe form can be accessed by authorized users', function () {
    $subscriber = Subscriber::factory()->create([
        'admin_id' => $this->admin->id,
        'unsubscribed_at' => now(),
        'is_subscribed' => false,
    ]);
    
    $response = $this->actingAs($this->admin)
        ->get(route('subscribers.resubscribe.form', $subscriber));
    
    $response->assertStatus(200);
    $response->assertViewIs('subscribers.resubscribe');
    $response->assertViewHas('subscriber', $subscriber);
});

test('resubscribe form redirects active subscribers', function () {
    $subscriber = Subscriber::factory()->create([
        'admin_id' => $this->admin->id,
        'unsubscribed_at' => null,
        'is_subscribed' => true,
    ]);
    
    $response = $this->actingAs($this->admin)
        ->get(route('subscribers.resubscribe.form', $subscriber));
    
    $response->assertRedirect(route('subscribers.show', $subscriber));
    $response->assertSessionHas('info', 'This subscriber is already active.');
});

test('resubscribe form requires authentication', function () {
    $subscriber = Subscriber::factory()->create();
    
    $response = $this->get(route('subscribers.resubscribe.form', $subscriber));
    $response->assertRedirect(route('magic-link.form'));
});