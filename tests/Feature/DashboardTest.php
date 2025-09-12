<?php

use App\Models\User;
use App\Models\Subscriber;
use App\Models\SubscriberAction;

describe('Admin Dashboard', function () {
    it('shows correct stats for regular admin', function () {
        $admin = User::factory()->create();
        $otherAdmin = User::factory()->create();
        
        // Create subscribers for this admin
        Subscriber::factory()->count(5)->create([
            'admin_id' => $admin->id,
            'is_subscribed' => true
        ]);
        
        // Create unsubscribed subscriber for this admin
        Subscriber::factory()->create([
            'admin_id' => $admin->id,
            'is_subscribed' => false
        ]);
        
        // Create subscribers for other admin (should not appear in stats)
        Subscriber::factory()->count(3)->create([
            'admin_id' => $otherAdmin->id
        ]);
        
        $this->actingAs($admin);
        
        $response = $this->get(route('dashboard'));
        
        $response->assertOk()
                ->assertViewIs('dashboard.admin')
                ->assertViewHas('totalSubscribers', 6)
                ->assertViewHas('activeSubscribers', 5)
                ->assertViewHas('unsubscribedCount', 1);
    });
    
    it('shows macOS version distribution for super admin', function () {
        $superAdmin = User::factory()->create(['is_super_admin' => true]);
        
        // Create subscribers across different admins with different subscribed versions
        $admin1 = User::factory()->create();
        $admin2 = User::factory()->create();
        
        Subscriber::factory()->count(3)->create([
            'admin_id' => $admin1->id,
            'subscribed_versions' => ['macOS 15']
        ]);
        
        Subscriber::factory()->count(2)->create([
            'admin_id' => $admin2->id,
            'subscribed_versions' => ['macOS 14']
        ]);
        
        Subscriber::factory()->create([
            'admin_id' => $admin1->id,
            'subscribed_versions' => ['macOS 13']
        ]);
        
        $this->actingAs($superAdmin);
        
        $response = $this->get(route('dashboard'));
        
        $versionStats = $response->viewData('versionStats');
        
        expect($versionStats)->toHaveKey('macOS 15', 3);
        expect($versionStats)->toHaveKey('macOS 14', 2);
        expect($versionStats)->toHaveKey('macOS 13', 1);
    });
    
    it('shows recent activity for admin', function () {
        $admin = User::factory()->create();
        $subscriber = Subscriber::factory()->create(['admin_id' => $admin->id]);
        
        // Create a version_changed action more recent than the auto-created subscribed action
        SubscriberAction::factory()->create([
            'subscriber_id' => $subscriber->id,
            'action' => 'version_changed',
            'data' => ['old_version' => 'macOS 14', 'new_version' => 'macOS 15'],
            'created_at' => now() // More recent than the auto-created action
        ]);
        
        $this->actingAs($admin);
        
        $response = $this->get(route('dashboard'));
        
        $recentActions = $response->viewData('recentActions');
        
        expect($recentActions)->toHaveCount(2);
        expect($recentActions->first()->action)->toBe('version_changed');
    });
});

describe('Super Admin Dashboard', function () {
    it('shows system-wide stats for super admin', function () {
        $superAdmin = User::factory()->create(['is_super_admin' => true]);
        
        // Create regular admins
        $admin1 = User::factory()->create();
        $admin2 = User::factory()->create();
        
        // Create subscribers for different admins
        Subscriber::factory()->count(4)->create(['admin_id' => $admin1->id]);
        Subscriber::factory()->count(3)->create(['admin_id' => $admin2->id]);
        
        // Create some unsubscribed
        Subscriber::factory()->count(2)->create([
            'admin_id' => $admin1->id,
            'is_subscribed' => false
        ]);
        
        $this->actingAs($superAdmin);
        
        $response = $this->get(route('dashboard'));
        
        $response->assertOk()
                ->assertViewIs('dashboard.super-admin')
                ->assertViewHas('totalAdmins', 3)
                ->assertViewHas('totalSubscribers', 9)
                ->assertViewHas('activeSubscribers', 7)
                ->assertViewHas('unsubscribedCount', 2);
    });
    
    it('shows admin performance stats', function () {
        $superAdmin = User::factory()->create(['is_super_admin' => true]);
        $admin1 = User::factory()->create(['name' => 'Admin One']);
        $admin2 = User::factory()->create(['name' => 'Admin Two']);
        
        // Create different numbers of subscribers for each admin
        Subscriber::factory()->count(5)->create(['admin_id' => $admin1->id]);
        Subscriber::factory()->count(3)->create(['admin_id' => $admin2->id]);
        
        $this->actingAs($superAdmin);
        
        $response = $this->get(route('dashboard'));
        
        $adminStats = $response->viewData('adminStats');
        
        expect($adminStats)->toHaveCount(2);
        
        $admin1Stats = $adminStats->firstWhere('id', $admin1->id);
        expect($admin1Stats->subscribers_count)->toBe(5);
        
        $admin2Stats = $adminStats->firstWhere('id', $admin2->id);
        expect($admin2Stats->subscribers_count)->toBe(3);
    });
    
    it('shows system-wide version distribution', function () {
        $superAdmin = User::factory()->create(['is_super_admin' => true]);
        $admin = User::factory()->create();
        
        // Create subscribers with different versions across the system
        Subscriber::factory()->count(4)->create([
            'admin_id' => $admin->id,
            'subscribed_versions' => ['macOS 15']
        ]);
        
        Subscriber::factory()->count(3)->create([
            'admin_id' => $admin->id,
            'subscribed_versions' => ['macOS 14']
        ]);
        
        $this->actingAs($superAdmin);
        
        $response = $this->get(route('dashboard'));
        
        $versionStats = $response->viewData('versionStats');
        
        expect($versionStats)->toHaveKey('macOS 15', 4);
        expect($versionStats)->toHaveKey('macOS 14', 3);
    });
    
    it('shows recent system activity', function () {
        $superAdmin = User::factory()->create(['is_super_admin' => true]);
        $admin = User::factory()->create();
        $subscriber = Subscriber::factory()->create(['admin_id' => $admin->id]);
        
        // Create 2 additional actions (1 is auto-created on subscriber creation)
        SubscriberAction::factory()->count(2)->create([
            'subscriber_id' => $subscriber->id,
            'action' => 'version_changed'
        ]);
        
        $this->actingAs($superAdmin);
        
        $response = $this->get(route('dashboard'));
        
        $recentActions = $response->viewData('recentActions');
        
        expect($recentActions)->toHaveCount(3);
    });
});

describe('Dashboard Access Control', function () {
    it('requires authentication to access dashboard', function () {
        $response = $this->get(route('dashboard'));
        
        $response->assertRedirect(route('magic-link.form'));
    });
    
    it('redirects admin away from super admin dashboard', function () {
        $admin = User::factory()->create();
        
        $this->actingAs($admin);
        
        $response = $this->get(route('dashboard.super-admin'));
        
        $response->assertForbidden();
    });
    
    it('allows super admin to access admin dashboard', function () {
        $superAdmin = User::factory()->create(['is_super_admin' => true]);
        
        $this->actingAs($superAdmin);
        
        $response = $this->get(route('dashboard.admin'));
        
        $response->assertOk()
                ->assertViewIs('dashboard.admin');
    });
});

describe('Dashboard Data Filtering', function () {
    it('admin only sees their own subscriber data', function () {
        $admin1 = User::factory()->create();
        $admin2 = User::factory()->create();
        
        // Create subscribers for both admins (auto-creates subscribed actions)
        $subscriber1 = Subscriber::factory()->create(['admin_id' => $admin1->id]);
        $subscriber2 = Subscriber::factory()->create(['admin_id' => $admin2->id]);
        
        $this->actingAs($admin1);
        
        $response = $this->get(route('dashboard'));
        
        // Should only see data for admin1's subscribers
        expect($response->viewData('totalSubscribers'))->toBe(1);
        expect($response->viewData('recentActions'))->toHaveCount(1);
        expect($response->viewData('recentActions')->first()->subscriber_id)->toBe($subscriber1->id);
    });
    
    it('shows correct metrics when subscribers unsubscribe', function () {
        $admin = User::factory()->create();
        
        // Create subscribed and unsubscribed subscribers
        Subscriber::factory()->count(3)->create([
            'admin_id' => $admin->id,
            'is_subscribed' => true
        ]);
        
        Subscriber::factory()->count(2)->create([
            'admin_id' => $admin->id,
            'is_subscribed' => false
        ]);
        
        $this->actingAs($admin);
        
        $response = $this->get(route('dashboard'));
        
        expect($response->viewData('totalSubscribers'))->toBe(5);
        expect($response->viewData('activeSubscribers'))->toBe(3);
        expect($response->viewData('unsubscribedCount'))->toBe(2);
    });
    
    it('calculates subscription rate correctly', function () {
        $admin = User::factory()->create();
        
        // Create 8 active and 2 unsubscribed (80% subscription rate)
        Subscriber::factory()->count(8)->create([
            'admin_id' => $admin->id,
            'is_subscribed' => true
        ]);
        
        Subscriber::factory()->count(2)->create([
            'admin_id' => $admin->id,
            'is_subscribed' => false
        ]);
        
        $this->actingAs($admin);
        
        $response = $this->get(route('dashboard'));
        
        expect($response->viewData('subscriptionRate'))->toBe(80.0);
    });
});
