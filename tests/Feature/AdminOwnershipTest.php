<?php

use App\Models\User;
use App\Models\Subscriber;

beforeEach(function () {
    // Create test users
    $this->superAdmin = User::factory()->create([
        'email' => 'superadmin@example.com',
        'name' => 'Super Admin',
        'is_super_admin' => true,
    ]);
    
    $this->regularAdmin = User::factory()->create([
        'email' => 'admin@example.com', 
        'name' => 'Regular Admin',
        'is_super_admin' => false,
    ]);
    
    $this->anotherAdmin = User::factory()->create([
        'email' => 'admin2@example.com',
        'name' => 'Another Admin', 
        'is_super_admin' => false,
    ]);
});

test('super admin can see admin ownership in subscriber list', function () {
    // Create a subscriber owned by the regular admin
    $subscriber = Subscriber::factory()->create([
        'admin_id' => $this->regularAdmin->id,
        'email' => 'subscriber@example.com',
    ]);

    // Login as super admin and use show_all to see all subscribers
    $response = $this->actingAs($this->superAdmin)->get(route('subscribers.index', ['show_all' => true]));

    $response->assertStatus(200);
    $response->assertSee($subscriber->email);
    $response->assertSee($this->regularAdmin->name); // Should see admin name
});

test('super admin can see admin ownership in subscriber show page', function () {
    // Create a subscriber owned by the regular admin
    $subscriber = Subscriber::factory()->create([
        'admin_id' => $this->regularAdmin->id,
        'email' => 'subscriber@example.com',
    ]);

    $response = $this->actingAs($this->superAdmin)->get(route('subscribers.show', $subscriber));

    $response->assertStatus(200);
    $response->assertSee('Administrator Ownership'); // Should see admin ownership section
    $response->assertSee($this->regularAdmin->name);
    $response->assertSee($this->regularAdmin->email);
});

test('super admin can see admin ownership in subscriber edit page', function () {
    // Create a subscriber owned by the regular admin
    $subscriber = Subscriber::factory()->create([
        'admin_id' => $this->regularAdmin->id,
        'email' => 'subscriber@example.com',
    ]);

    $response = $this->actingAs($this->superAdmin)->get(route('subscribers.edit', $subscriber));

    $response->assertStatus(200);
    $response->assertSee('Administrator Ownership'); // Should see admin ownership section
    $response->assertSee($this->regularAdmin->name);
    $response->assertSee('Responsible Administrator');
});

test('regular admin cannot see admin ownership in subscriber list', function () {
    // Create a subscriber owned by the regular admin
    $subscriber = Subscriber::factory()->create([
        'admin_id' => $this->regularAdmin->id,
        'email' => 'subscriber@example.com',
    ]);

    $response = $this->actingAs($this->regularAdmin)->get(route('subscribers.index'));

    $response->assertStatus(200);
    $response->assertSee($subscriber->email);
    // Should NOT see admin ownership badge/information for regular admin
    $response->assertDontSee('Super Admin'); // Ensure no super admin specific content
});

test('regular admin cannot see admin ownership in subscriber show page', function () {
    // Create a subscriber owned by the regular admin
    $subscriber = Subscriber::factory()->create([
        'admin_id' => $this->regularAdmin->id,
        'email' => 'subscriber@example.com',
    ]);

    $response = $this->actingAs($this->regularAdmin)->get(route('subscribers.show', $subscriber));

    $response->assertStatus(200);
    $response->assertDontSee('Administrator Ownership'); // Should NOT see admin ownership section
});

test('regular admin cannot see admin ownership in subscriber edit page', function () {
    // Create a subscriber owned by the regular admin
    $subscriber = Subscriber::factory()->create([
        'admin_id' => $this->regularAdmin->id,
        'email' => 'subscriber@example.com',
    ]);

    $response = $this->actingAs($this->regularAdmin)->get(route('subscribers.edit', $subscriber));

    $response->assertStatus(200);
    $response->assertDontSee('Administrator Ownership'); // Should NOT see admin ownership section
    $response->assertDontSee('Responsible Administrator');
});

test('new subscribers are assigned to creating admin', function () {
    $response = $this->actingAs($this->regularAdmin)->post(route('subscribers.store'), [
        'email' => 'newsubscriber@example.com',
        'subscribed_versions' => ['macOS 14'],
        'days_to_install' => 30,
    ]);

    $response->assertRedirect(route('subscribers.index'));
    
    // Check that subscriber was assigned to the creating admin
    $subscriber = Subscriber::where('email', 'newsubscriber@example.com')->first();
    expect($subscriber)->not->toBeNull();
    expect($subscriber->admin_id)->toBe($this->regularAdmin->id);
});

test('admin cannot access subscribers from other admins', function () {
    // Create a subscriber owned by another admin
    $subscriber = Subscriber::factory()->create([
        'admin_id' => $this->anotherAdmin->id,
        'email' => 'othersubscriber@example.com',
    ]);

    // Try to access the other admin's subscriber
    $response = $this->actingAs($this->regularAdmin)->get(route('subscribers.show', $subscriber));
    $response->assertStatus(403); // Should be forbidden
});

test('super admin can access subscribers from all admins', function () {
    // Create subscribers owned by different admins
    $subscriber1 = Subscriber::factory()->create([
        'admin_id' => $this->regularAdmin->id,
        'email' => 'subscriber1@example.com',
    ]);
    
    $subscriber2 = Subscriber::factory()->create([
        'admin_id' => $this->anotherAdmin->id,
        'email' => 'subscriber2@example.com',
    ]);

    // Should be able to access both subscribers
    $response1 = $this->actingAs($this->superAdmin)->get(route('subscribers.show', $subscriber1));
    $response1->assertStatus(200);
    
    $response2 = $this->actingAs($this->superAdmin)->get(route('subscribers.show', $subscriber2));
    $response2->assertStatus(200);
});

test('subscriber without admin displays gracefully', function () {
    // Create a subscriber without admin (edge case)
    $subscriber = Subscriber::factory()->create([
        'admin_id' => null,
        'email' => 'orphan@example.com',
    ]);

    // Visit subscriber pages - should not break
    // Use show_all=true to see orphaned subscribers
    $response = $this->actingAs($this->superAdmin)->get(route('subscribers.index', ['show_all' => true]));
    $response->assertStatus(200);
    $response->assertSee($subscriber->email);

    $response = $this->actingAs($this->superAdmin)->get(route('subscribers.show', $subscriber));
    $response->assertStatus(200);
    $response->assertSee($subscriber->email);
});

test('comprehensive macos versions are available in edit form', function () {
    // Create some releases in the database to ensure we have database versions
    \App\Models\Release::factory()->create(['major_version' => 'macOS 14']);
    \App\Models\Release::factory()->create(['major_version' => 'macOS 15']);
    \App\Models\Release::factory()->create(['major_version' => 'macOS 13']);
    
    // Create a subscriber
    $subscriber = Subscriber::factory()->create([
        'admin_id' => $this->regularAdmin->id,
    ]);

    // Visit edit page
    $response = $this->actingAs($this->regularAdmin)->get(route('subscribers.edit', $subscriber));

    $response->assertStatus(200);
    
    // Should see database versions
    $response->assertSee('macOS 13');    // Ventura
    $response->assertSee('macOS 14');    // Sonoma
    $response->assertSee('macOS 15');    // Sequoia
});

test('fallback macos versions are used when no database versions exist', function () {
    // Clear all releases from database
    \App\Models\Release::truncate();
    
    // Create a subscriber
    $subscriber = Subscriber::factory()->create([
        'admin_id' => $this->regularAdmin->id,
    ]);

    // Visit edit page
    $response = $this->actingAs($this->regularAdmin)->get(route('subscribers.edit', $subscriber));

    $response->assertStatus(200);
    
    // Should see fallback versions
    $response->assertSee('macOS 10.15'); // Catalina
    $response->assertSee('macOS 11');    // Big Sur
    $response->assertSee('macOS 12');    // Monterey
    $response->assertSee('macOS 13');    // Ventura
    $response->assertSee('macOS 14');    // Sonoma
    $response->assertSee('macOS 15');    // Sequoia
    $response->assertSee('macOS 26');    // Tahoe
});

test('macos version format is consistent in admin user show page', function () {
    // Create a subscriber
    $subscriber = Subscriber::factory()->create([
        'admin_id' => $this->regularAdmin->id,
        'subscribed_versions' => ['macOS 14', 'macOS 15'],
    ]);

    // Visit admin user show page
    $response = $this->actingAs($this->superAdmin)->get(route('admin.users.show', $this->regularAdmin));

    $response->assertStatus(200);
    
    // Should see consistent macOS version format
    $response->assertSee('macOS 14');
    $response->assertSee('macOS 15');
    
    // Should NOT see version names like "Ventura", "Sonoma" etc.
    $response->assertDontSee('Ventura');
    $response->assertDontSee('Sonoma');
    $response->assertDontSee('Sequoia');
});
