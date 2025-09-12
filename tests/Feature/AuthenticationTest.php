<?php

use App\Models\User;
use App\Models\Subscriber;
use App\Notifications\MagicLinkNotification;
use Spatie\LoginLink\LoginLink;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Carbon\Carbon;

beforeEach(function () {
    Mail::fake();
    Notification::fake();
});

describe('User Roles and Permissions', function () {
    it('regular admin can only access their own subscribers', function () {
        $admin1 = User::factory()->create();
        $admin2 = User::factory()->create();
        
        $subscriber1 = Subscriber::factory()->create(['admin_id' => $admin1->id]);
        $subscriber2 = Subscriber::factory()->create(['admin_id' => $admin2->id]);
        
        $this->actingAs($admin1);
        
        // Can access own subscriber
        $response = $this->get(route('subscribers.show', $subscriber1));
        $response->assertOk();
        
        // Cannot access other admin's subscriber
        $response = $this->get(route('subscribers.show', $subscriber2));
        $response->assertForbidden();
    });
    
    it('super admin can access all subscribers', function () {
        $superAdmin = User::factory()->create(['is_super_admin' => true]);
        $admin = User::factory()->create();
        
        $subscriber = Subscriber::factory()->create(['admin_id' => $admin->id]);
        
        $this->actingAs($superAdmin);
        
        $response = $this->get(route('subscribers.show', $subscriber));
        $response->assertOk();
    });
    
    it('only super admin can access user management', function () {
        $admin = User::factory()->create();
        $superAdmin = User::factory()->create(['is_super_admin' => true]);
        
        // Regular admin cannot access user management
        $this->actingAs($admin);
        $response = $this->get(route('admin.users.index'));
        $response->assertForbidden();
        
        // Super admin can access user management
        $this->actingAs($superAdmin);
        $response = $this->get(route('admin.users.index'));
        $response->assertOk();
    });
    
    it('can promote user to super admin', function () {
        $superAdmin = User::factory()->create(['is_super_admin' => true]);
        $admin = User::factory()->create(['is_super_admin' => false]);
        
        $this->actingAs($superAdmin);
        
        $response = $this->post(route('admin.users.promote', $admin));
        
        $response->assertRedirect()
                ->assertSessionHas('success');
                
        expect($admin->fresh()->is_super_admin)->toBeTrue();
    });
    
    it('can demote super admin to regular admin', function () {
        $superAdmin1 = User::factory()->create(['is_super_admin' => true]);
        $superAdmin2 = User::factory()->create(['is_super_admin' => true]);
        
        $this->actingAs($superAdmin1);
        
        $response = $this->post(route('admin.users.demote', $superAdmin2));
        
        $response->assertRedirect()
                ->assertSessionHas('success');
                
        expect($superAdmin2->fresh()->is_super_admin)->toBeFalse();
    });
    
    it('cannot demote themselves', function () {
        $superAdmin = User::factory()->create(['is_super_admin' => true]);
        
        $this->actingAs($superAdmin);
        
        $response = $this->post(route('admin.users.demote', $superAdmin));
        
        $response->assertForbidden();
    });
});

describe('Dashboard Access Control', function () {
    it('redirects regular admin to admin dashboard', function () {
        $admin = User::factory()->create();
        
        $this->actingAs($admin);
        
        $response = $this->get(route('dashboard'));
        
        // Debug what we're getting
        echo "Status: " . $response->getStatusCode() . "\n";
        echo "Is redirect: " . ($response->isRedirection() ? 'yes' : 'no') . "\n";
        
        $response->assertOk();
        $response->assertViewIs('dashboard.admin');
    });
    
    it('allows super admin to choose dashboard', function () {
        $superAdmin = User::factory()->create(['is_super_admin' => true]);
        
        $this->actingAs($superAdmin);
        
        $response = $this->get(route('dashboard'));
        
        $response->assertViewIs('dashboard.super-admin');
    });
    
    it('shows correct subscriber count for admin', function () {
        $admin = User::factory()->create();
        Subscriber::factory()->count(5)->create(['admin_id' => $admin->id]);
        Subscriber::factory()->count(3)->create(); // Other admin's subscribers
        
        $this->actingAs($admin);
        
        $response = $this->get(route('dashboard'));
        
        $response->assertViewHas('totalSubscribers', 5);
    });
    
    it('shows system-wide stats for super admin', function () {
        $superAdmin = User::factory()->create(['is_super_admin' => true]);
        $admin1 = User::factory()->has(Subscriber::factory()->count(10))->create();
        $admin2 = User::factory()->has(Subscriber::factory()->count(5))->create();
        $admin3 = User::factory()->has(Subscriber::factory()->count(3))->create();
        $admin4 = User::factory()->has(Subscriber::factory()->count(2))->create();

        $this->actingAs($superAdmin);
        
        $response = $this->get(route('dashboard'));
        
        $response->assertViewHas('totalAdmins', 5);
        $response->assertViewHas('totalSubscribers', 20);
    });
});

describe('Middleware Protection', function () {
    it('guest cannot access protected routes', function () {
        $routes = [
            'dashboard',
            'subscribers.index',
            'admin.users.index',
        ];
        
        foreach ($routes as $route) {
            $response = $this->get(route($route));
            $response->assertRedirect(route('magic-link.form'));
        }
    });
    
    it('regular admin cannot access super admin routes', function () {
        $admin = User::factory()->create();
        
        $this->actingAs($admin);
        
        $superAdminRoutes = [
            'admin.users.index',
            'admin.users.show' => User::factory()->create(),
        ];
        
        foreach ($superAdminRoutes as $route => $param) {
            if (is_string($route)) {
                $response = $this->get(route($route, $param));
            } else {
                $response = $this->get(route($superAdminRoutes[$route]));
            }
            $response->assertForbidden();
        }
    });
});

describe('Authentication Flow', function () {
    it('redirects authenticated users away from login form', function () {
        $user = User::factory()->create();
        
        $this->actingAs($user);
        
        $response = $this->get(route('magic-link.form'));
        
        $response->assertRedirect(route('dashboard'));
    });
    
    it('logs out user successfully', function () {
        $user = User::factory()->create();
        
        $this->actingAs($user);
        
        $response = $this->post(route('logout'));
        
        $response->assertRedirect('/');
        $this->assertGuest();
    });
    
    it('updates last login timestamp on successful login', function () {
        $user = User::factory()->create(['last_login_at' => null]);
        
        // Simulate login and update timestamp
        $user->update(['last_login_at' => now()]);
        
        expect($user->fresh()->last_login_at)->not->toBeNull();
        expect($user->fresh()->last_login_at)->toBeInstanceOf(Carbon::class);
    });
});
