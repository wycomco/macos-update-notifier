<?php

use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('application loads successfully', function () {
    $response = $this->get('/');
    $response->assertStatus(200);
});

test('magic link form is accessible', function () {
    $response = $this->get(route('magic-link.form'));
    $response->assertStatus(200);
});

test('can create a user', function () {
    $user = \App\Models\User::factory()->create([
        'email' => 'test@example.com',
        'is_super_admin' => false
    ]);
    
    expect($user)->toBeInstanceOf(\App\Models\User::class);
    expect($user->email)->toBe('test@example.com');
    expect($user->is_super_admin)->toBeFalse();
});

test('user can be promoted to super admin', function () {
    $user = \App\Models\User::factory()->create(['is_super_admin' => false]);
    
    $user->promoteToSuperAdmin();
    
    expect($user->fresh()->is_super_admin)->toBeTrue();
});

test('subscribers belong to admin', function () {
    $admin = \App\Models\User::factory()->create();
    $subscriber = \App\Models\Subscriber::factory()->create([
        'admin_id' => $admin->id,
        'email' => 'subscriber@example.com'
    ]);
    
    expect($subscriber->admin_id)->toBe($admin->id);
    expect($admin->subscribers()->count())->toBe(1);
    expect($subscriber->admin->email)->toBe($admin->email);
});
