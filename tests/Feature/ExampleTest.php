<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    installApplication();
});

it('shows the welcome page to guests', function () {
    $response = $this->get('/');

    $response->assertStatus(200);
});

it('redirects authenticated users from the root to the dashboard', function () {
    $user = User::factory()->create();

    $this->actingAs($user, 'auth0-session');

    $this->get('/')->assertRedirect(route('dashboard'));
});
