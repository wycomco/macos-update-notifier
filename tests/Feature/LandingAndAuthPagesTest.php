<?php

use function Pest\Laravel\get;

it('renders the landing page', function () {
    $response = get('/');
    $response->assertStatus(200);
    $response->assertSee('Stay ahead of', false);
    $response->assertSee('macOS releases', false);
});

it('renders the login page', function () {
    $response = get(route('login'));
    $response->assertStatus(200);
    $response->assertSee('Welcome back', false);
});

it('renders the register page', function () {
    $response = get(route('register'));
    $response->assertStatus(200);
    $response->assertSee('Create your account', false);
});

it('renders the magic link form', function () {
    $response = get(route('magic-link.form'));
    $response->assertStatus(200);
    $response->assertSee('Magic Link Sign In', false);
});
