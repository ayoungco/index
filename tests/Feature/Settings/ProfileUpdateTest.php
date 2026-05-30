<?php

use App\Models\User;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

beforeEach(function () {
    installApplication();
});

test('profile page is displayed', function () {
    $this->actingAs($user = User::factory()->create(), 'auth0-session');

    $this->get('/settings/profile')->assertOk();
});

test('profile information can be updated', function () {
    $user = User::factory()->create();

    $this->actingAs($user, 'auth0-session');

    $response = $this->put('/settings/profile', [
        'name' => 'Test User',
        'email' => 'test@example.com',
    ]);

    $response->assertSessionHasNoErrors();

    $user->refresh();

    expect($user->name)->toEqual('Test User');
    expect($user->email)->toEqual('test@example.com');
    expect($user->email_verified_at)->toBeNull();
});

test('email verification status is unchanged when email address is unchanged', function () {
    $user = User::factory()->create();

    $this->actingAs($user, 'auth0-session');

    $response = $this->put('/settings/profile', [
        'name' => 'Test User',
        'email' => $user->email,
    ]);

    $response->assertSessionHasNoErrors();

    expect($user->refresh()->email_verified_at)->not->toBeNull();
});
