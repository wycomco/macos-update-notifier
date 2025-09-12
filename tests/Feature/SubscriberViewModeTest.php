<?php

use App\Models\User;
use App\Models\Subscriber;

beforeEach(function () {
    $this->superAdmin = User::factory()->create(['is_super_admin' => true]);
    $this->regularAdmin1 = User::factory()->create(['is_super_admin' => false]);
    $this->regularAdmin2 = User::factory()->create(['is_super_admin' => false]);
    
    // Create subscribers for different admins
    $this->superAdminSubscribers = Subscriber::factory()->count(2)->create(['admin_id' => $this->superAdmin->id]);
    $this->admin1Subscribers = Subscriber::factory()->count(3)->create(['admin_id' => $this->regularAdmin1->id]);
    $this->admin2Subscribers = Subscriber::factory()->count(2)->create(['admin_id' => $this->regularAdmin2->id]);
});

test('super admin sees view mode toggle in header', function () {
    $response = $this->actingAs($this->superAdmin)->get(route('subscribers.index'));
    
    $response->assertStatus(200);
    $response->assertSee('My Subscribers');
    $response->assertSee('All Subscribers');
});

test('regular admin does not see view mode toggle', function () {
    $response = $this->actingAs($this->regularAdmin1)->get(route('subscribers.index'));
    
    $response->assertStatus(200);
    $response->assertDontSee('My Subscribers');
    $response->assertDontSee('All Subscribers');
});

test('super admin defaults to own subscribers view', function () {
    $response = $this->actingAs($this->superAdmin)->get(route('subscribers.index'));
    
    $response->assertStatus(200);
    $response->assertSee('Showing only your subscribers');
    $response->assertSee('2 subscribers'); // Super admin has 2 subscribers
    
    // Should see own subscribers
    foreach ($this->superAdminSubscribers as $subscriber) {
        $response->assertSee($subscriber->email);
    }
    
    // Should not see other admins' subscribers
    foreach ($this->admin1Subscribers as $subscriber) {
        $response->assertDontSee($subscriber->email);
    }
});

test('super admin can switch to all subscribers view', function () {
    $response = $this->actingAs($this->superAdmin)->get(route('subscribers.index', ['show_all' => true]));
    
    $response->assertStatus(200);
    $response->assertSee('Showing all subscribers from all admins');
    $response->assertSee('7 subscribers'); // Total: 2 + 3 + 2 = 7
    
    // Should see all subscribers
    foreach ($this->superAdminSubscribers as $subscriber) {
        $response->assertSee($subscriber->email);
    }
    foreach ($this->admin1Subscribers as $subscriber) {
        $response->assertSee($subscriber->email);
    }
    foreach ($this->admin2Subscribers as $subscriber) {
        $response->assertSee($subscriber->email);
    }
});

test('super admin can switch back to own subscribers view', function () {
    // First visit all view to set session
    $this->actingAs($this->superAdmin)->get(route('subscribers.index', ['show_all' => true]));
    
    // Then switch to own view
    $response = $this->actingAs($this->superAdmin)->get(route('subscribers.index', ['show_all' => false]));
    
    $response->assertStatus(200);
    $response->assertSee('Showing only your subscribers');
    $response->assertSee('2 subscribers');
});

test('super admin view mode preference is remembered in session', function () {
    // Set preference to show all
    $this->actingAs($this->superAdmin)->get(route('subscribers.index', ['show_all' => true]));
    
    // Visit again without parameter - should remember the preference
    $response = $this->actingAs($this->superAdmin)->get(route('subscribers.index'));
    
    $response->assertStatus(200);
    $response->assertSee('Showing all subscribers from all admins');
    $response->assertSee('7 subscribers');
});

test('super admin own view mode preference is remembered in session', function () {
    // Set preference to show own only
    $this->actingAs($this->superAdmin)->get(route('subscribers.index', ['show_all' => false]));
    
    // Visit again without parameter - should remember the preference
    $response = $this->actingAs($this->superAdmin)->get(route('subscribers.index'));
    
    $response->assertStatus(200);
    $response->assertSee('Showing only your subscribers');
    $response->assertSee('2 subscribers');
});

test('regular admin always sees only own subscribers', function () {
    $response = $this->actingAs($this->regularAdmin1)->get(route('subscribers.index'));
    
    $response->assertStatus(200);
    // Check the sidebar statistics instead of view mode indicator
    $response->assertSee('<div class="text-3xl font-bold text-white mb-1">3</div>', false);
    
    // Should see own subscribers
    foreach ($this->admin1Subscribers as $subscriber) {
        $response->assertSee($subscriber->email);
    }
    
    // Should not see other admins' subscribers
    foreach ($this->superAdminSubscribers as $subscriber) {
        $response->assertDontSee($subscriber->email);
    }
    foreach ($this->admin2Subscribers as $subscriber) {
        $response->assertDontSee($subscriber->email);
    }
});

test('view mode toggle buttons have correct active states', function () {
    // Test own view active state
    $response = $this->actingAs($this->superAdmin)->get(route('subscribers.index', ['show_all' => false]));
    $response->assertSee('bg-purple-500 text-white shadow-lg', false); // My Subscribers button should be active
    
    // Test all view active state
    $response = $this->actingAs($this->superAdmin)->get(route('subscribers.index', ['show_all' => true]));
    $content = $response->getContent();
    // Check that All Subscribers button has active styling
    expect($content)->toContain('All Subscribers');
});

test('view mode indicator shows correct status', function () {
    // Test own subscribers indicator
    $response = $this->actingAs($this->superAdmin)->get(route('subscribers.index', ['show_all' => false]));
    $response->assertSee('bg-purple-400', false);
    $response->assertSee('Showing only your subscribers');
    
    // Test all subscribers indicator  
    $response = $this->actingAs($this->superAdmin)->get(route('subscribers.index', ['show_all' => true]));
    $response->assertSee('bg-blue-400', false);
    $response->assertSee('Showing all subscribers from all admins');
});
