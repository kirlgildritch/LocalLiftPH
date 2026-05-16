<?php

use App\Models\User;

test('login screen can be rendered', function () {
    $response = $this->get('/login');

    $response->assertStatus(200);
});

test('users can authenticate using the login screen', function () {
    $user = User::factory()->create();

    $response = $this->post('/login', [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(route('dashboard', absolute: false));
});

test('users can not authenticate with invalid password', function () {
    $user = User::factory()->create();

    $this->post('/login', [
        'email' => $user->email,
        'password' => 'wrong-password',
    ]);

    $this->assertGuest();
});

test('users can logout', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post('/logout');

    $this->assertGuest();
    $response->assertRedirect('/');
});

test('seller can authenticate only through seller guard', function () {
    $seller = User::factory()->create([
        'is_seller' => true,
        'is_admin' => false,
    ]);

    $response = $this->post(route('seller.login.store'), [
        'email' => $seller->email,
        'password' => 'password',
    ]);

    $this->assertAuthenticatedAs($seller, 'seller');
    $response->assertRedirect(route('seller.dashboard'));
});

test('buyer cannot authenticate through seller guard', function () {
    $buyer = User::factory()->create([
        'is_seller' => false,
        'is_admin' => false,
    ]);

    $response = $this->post(route('seller.login.store'), [
        'email' => $buyer->email,
        'password' => 'password',
    ]);

    $this->assertGuest('seller');
    $response
        ->assertRedirect(route('seller.login'))
        ->assertSessionHasErrors('email');
});

test('admin can authenticate only through admin guard', function () {
    $admin = User::factory()->create([
        'is_seller' => false,
        'is_admin' => true,
    ]);

    $response = $this->post(route('admin.login.store'), [
        'email' => $admin->email,
        'password' => 'password',
    ]);

    $this->assertAuthenticatedAs($admin, 'admin');
    $response->assertRedirect(route('admin.dashboard'));
});

test('non admin cannot authenticate through admin guard', function () {
    $seller = User::factory()->create([
        'is_seller' => true,
        'is_admin' => false,
    ]);

    $response = $this->post(route('admin.login.store'), [
        'email' => $seller->email,
        'password' => 'password',
    ]);

    $this->assertGuest('admin');
    $response
        ->assertRedirect(route('admin.login'))
        ->assertSessionHasErrors('email');
});
