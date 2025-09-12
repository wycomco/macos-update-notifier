<?php

use App\Models\User;
use App\Models\Subscriber;
use App\Models\SubscriberAction;

describe('User Management', function () {
    it('super admin can view user list', function () {
        $superAdmin = User::factory()->create(['is_super_admin' => true]);
        
        // Create some regular admins
        User::factory()->count(3)->create(['is_super_admin' => false]);
        
        $this->actingAs($superAdmin);
        
        $response = $this->get(route('admin.users.index'));
        
        $response->assertOk()
                ->assertViewIs('admin.users.index')
                ->assertViewHas('users');
                
        // Should show all users including super admin (at least 4)
        expect($response->viewData('users')->total())->toBeGreaterThanOrEqual(4);
    });
    
    it('regular admin cannot access user management', function () {
        $admin = User::factory()->create(['is_super_admin' => false]);
        
        $this->actingAs($admin);
        
        $response = $this->get(route('admin.users.index'));
        
        $response->assertForbidden();
    });
    
    it('super admin can view individual user details', function () {
        $superAdmin = User::factory()->create(['is_super_admin' => true]);
        $admin = User::factory()->create(['name' => 'Test Admin']);
        
        // Create some subscribers for the admin
        Subscriber::factory()->count(3)->create(['admin_id' => $admin->id]);
        
        // Create some recent actions
        $subscriber = Subscriber::factory()->create(['admin_id' => $admin->id]);
        SubscriberAction::factory()->count(2)->create([
            'subscriber_id' => $subscriber->id,
            'action' => 'subscribed'
        ]);
        
        $this->actingAs($superAdmin);
        
        $response = $this->get(route('admin.users.show', $admin));
        
        $response->assertOk()
                ->assertViewIs('admin.users.show')
                ->assertViewHas('user', $admin)
                ->assertViewHas('recentActions');
        
        // Check that user's subscribers are loaded
        expect($admin->fresh()->subscribers)->toHaveCount(4);
    });
    
    it('super admin can promote user to super admin', function () {
        $superAdmin = User::factory()->create(['is_super_admin' => true]);
        $admin = User::factory()->create(['is_super_admin' => false]);
        
        $this->actingAs($superAdmin);
        
        $response = $this->post(route('admin.users.promote', $admin));
        
        $response->assertRedirect()
                ->assertSessionHas('success');
        
        expect($admin->fresh()->is_super_admin)->toBeTrue();
    });
    
    it('super admin can demote other super admin', function () {
        $superAdmin1 = User::factory()->create(['is_super_admin' => true]);
        $superAdmin2 = User::factory()->create(['is_super_admin' => true]);
        
        $this->actingAs($superAdmin1);
        
        $response = $this->post(route('admin.users.demote', $superAdmin2));
        
        $response->assertRedirect()
                ->assertSessionHas('success');
        
        expect($superAdmin2->fresh()->is_super_admin)->toBeFalse();
    });
    
    it('super admin cannot demote themselves', function () {
        $superAdmin = User::factory()->create(['is_super_admin' => true]);
        
        $this->actingAs($superAdmin);
        
        $response = $this->post(route('admin.users.demote', $superAdmin));
        
        $response->assertForbidden();
    });
    
    it('super admin can delete other users', function () {
        $superAdmin = User::factory()->create(['is_super_admin' => true]);
        $admin = User::factory()->create(['is_super_admin' => false]);
        
        // Create some subscribers for the admin
        $subscribers = Subscriber::factory()->count(3)->create(['admin_id' => $admin->id]);
        $subscriberIds = $subscribers->pluck('id');
        
        $this->actingAs($superAdmin);
        
        $response = $this->delete(route('admin.users.destroy', $admin));
        
        $response->assertRedirect()
                ->assertSessionHas('success');
        
        // User should be deleted
        expect(User::find($admin->id))->toBeNull();
        
        // Subscribers should also be deleted due to cascade constraint
        expect(Subscriber::whereIn('id', $subscriberIds)->count())->toBe(0);
    });
    
    it('super admin cannot delete themselves', function () {
        $superAdmin = User::factory()->create(['is_super_admin' => true]);
        
        $this->actingAs($superAdmin);
        
        $response = $this->delete(route('admin.users.destroy', $superAdmin));
        
        $response->assertForbidden();
    });
    
    it('regular admin cannot delete users', function () {
        $admin = User::factory()->create(['is_super_admin' => false]);
        $otherAdmin = User::factory()->create(['is_super_admin' => false]);
        
        $this->actingAs($admin);
        
        $response = $this->delete(route('admin.users.destroy', $otherAdmin));
        
        $response->assertForbidden();
    });
    
    it('deleting user with no subscribers works correctly', function () {
        $superAdmin = User::factory()->create(['is_super_admin' => true]);
        $admin = User::factory()->create(['is_super_admin' => false]);
        
        // No subscribers for this admin
        
        $this->actingAs($superAdmin);
        
        $response = $this->delete(route('admin.users.destroy', $admin));
        
        $response->assertRedirect()
                ->assertSessionHas('success');
        
        expect(User::find($admin->id))->toBeNull();
    });
    
    it('success message mentions subscriber deletion count', function () {
        $superAdmin = User::factory()->create(['is_super_admin' => true]);
        $admin = User::factory()->create(['is_super_admin' => false]);
        
        // Create 2 subscribers for the admin
        Subscriber::factory()->count(2)->create(['admin_id' => $admin->id]);
        
        $this->actingAs($superAdmin);
        
        $response = $this->delete(route('admin.users.destroy', $admin));
        
        $response->assertRedirect()
                ->assertSessionHas('success')
                ->assertSessionHasNoErrors();
        
        // Check that the success message mentions the subscriber count
        $successMessage = session('success');
        expect($successMessage)->toContain('2 subscriber(s) have also been deleted');
    });
    
    it('regular admin cannot promote users', function () {
        $admin = User::factory()->create(['is_super_admin' => false]);
        $otherAdmin = User::factory()->create(['is_super_admin' => false]);
        
        $this->actingAs($admin);
        
        $response = $this->post(route('admin.users.promote', $otherAdmin));
        
        $response->assertForbidden();
    });
    
    it('shows user with subscriber count in listing', function () {
        $superAdmin = User::factory()->create(['is_super_admin' => true]);
        $admin = User::factory()->create();
        
        // Create subscribers for admin
        Subscriber::factory()->count(5)->create(['admin_id' => $admin->id]);
        
        $this->actingAs($superAdmin);
        
        $response = $this->get(route('admin.users.index'));
        
        $users = $response->viewData('users');
        $adminFromList = $users->firstWhere('id', $admin->id);
        
        expect($adminFromList->subscribers_count)->toBe(5);
    });
    
    it('shows last login information', function () {
        $superAdmin = User::factory()->create(['is_super_admin' => true]);
        $admin = User::factory()->create([
            'last_login_at' => now()->subDays(2)
        ]);
        
        $this->actingAs($superAdmin);
        
        $response = $this->get(route('admin.users.show', $admin));
        
        $response->assertOk()
                ->assertSee('2 days ago'); // Should show last login in human format
    });
    
    it('handles user with no subscribers gracefully', function () {
        $superAdmin = User::factory()->create(['is_super_admin' => true]);
        $admin = User::factory()->create();
        
        // No subscribers created for this admin
        
        $this->actingAs($superAdmin);
        
        $response = $this->get(route('admin.users.show', $admin));
        
        $response->assertOk()
                ->assertSee('not managing any subscribers');
    });
    
    it('shows recent actions for user subscribers', function () {
        $superAdmin = User::factory()->create(['is_super_admin' => true]);
        $admin = User::factory()->create();
        
        // Create subscribers (auto-creates 'subscribed' actions)
        $subscriber1 = Subscriber::factory()->create(['admin_id' => $admin->id]);
        $subscriber2 = Subscriber::factory()->create(['admin_id' => $admin->id]);
        
        // Create action for different admin's subscriber (should not appear)
        $otherAdmin = User::factory()->create();
        $otherSubscriber = Subscriber::factory()->create(['admin_id' => $otherAdmin->id]);
        
        $this->actingAs($superAdmin);
        
        $response = $this->get(route('admin.users.show', $admin));
        
        $recentActions = $response->viewData('recentActions');
        
        expect($recentActions)->toHaveCount(2);
        expect($recentActions->pluck('subscriber_id'))->not->toContain($otherSubscriber->id);
    });
});

describe('User Management Validation', function () {
    it('validates promote request has valid user', function () {
        $superAdmin = User::factory()->create(['is_super_admin' => true]);
        
        $this->actingAs($superAdmin);
        
        $response = $this->post(route('admin.users.promote', 999)); // Non-existent user
        
        $response->assertNotFound();
    });
    
    it('validates demote request has valid user', function () {
        $superAdmin = User::factory()->create(['is_super_admin' => true]);
        
        $this->actingAs($superAdmin);
        
        $response = $this->post(route('admin.users.demote', 999)); // Non-existent user
        
        $response->assertNotFound();
    });
    
    it('cannot promote already super admin', function () {
        $superAdmin1 = User::factory()->create(['is_super_admin' => true]);
        $superAdmin2 = User::factory()->create(['is_super_admin' => true]);
        
        $this->actingAs($superAdmin1);
        
        $response = $this->post(route('admin.users.promote', $superAdmin2));
        
        $response->assertRedirect()
                ->assertSessionHas('error'); // Should show error that user is already super admin
    });
    
    it('cannot demote regular admin', function () {
        $superAdmin = User::factory()->create(['is_super_admin' => true]);
        $admin = User::factory()->create(['is_super_admin' => false]);
        
        $this->actingAs($superAdmin);
        
        $response = $this->post(route('admin.users.demote', $admin));
        
        $response->assertRedirect()
                ->assertSessionHas('error'); // Should show error that user is not super admin
    });
    
    it('validates delete request has valid user', function () {
        $superAdmin = User::factory()->create(['is_super_admin' => true]);
        
        $this->actingAs($superAdmin);
        
        $response = $this->delete(route('admin.users.destroy', 999)); // Non-existent user
        
        $response->assertNotFound();
    });
});
