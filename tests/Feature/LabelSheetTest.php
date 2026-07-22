<?php

use App\Models\Item;
use App\Models\User;
use Auth0\Laravel\Middleware\AuthenticatorMiddleware;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    installApplication();
    $this->withoutMiddleware(AuthenticatorMiddleware::class);
});

test('authenticated users can generate an uninitialized QR label sheet', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('labels.print'), ['quantity' => 3]);

    $response->assertOk();
    $response->assertSee('Printable QR labels');
    expect(Item::query()->count())->toBe(0);
    expect(substr_count($response->getContent(), '<article class="label">'))->toBe(3);
});

test('label sheet limits a single print run to thirty labels', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->post(route('labels.print'), ['quantity' => 31])
        ->assertSessionHasErrors('quantity');
});
