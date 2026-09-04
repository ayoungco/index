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

    $response = $this->actingAs($user)->post(route('labels.print'), ['quantity' => 3, 'media_width' => 4, 'media_height' => 6, 'columns' => 1, 'rows' => 3]);

    $response->assertOk();
    $response->assertSee('Printable QR labels');
    expect(Item::query()->count())->toBe(0);
    expect(substr_count($response->getContent(), '<article class="label">'))->toBe(3);
});

test('label sheet limits a single print run to thirty labels', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->post(route('labels.print'), ['quantity' => 31, 'media_width' => 4, 'media_height' => 6, 'columns' => 2, 'rows' => 15])
        ->assertSessionHasErrors('quantity');
});

test('label sheet uses the selected media size and grid', function () {
    $user = User::factory()->create();
    $response = $this->actingAs($user)->post(route('labels.print'), ['quantity' => 4, 'media_width' => 8, 'media_height' => 12, 'columns' => 2, 'rows' => 2]);
    $response->assertOk()->assertSee('repeat(2, minmax(0, 1fr))', false)->assertSee('width: 8in', false)->assertSee('height: 12in', false)->assertDontSee('label__url', false);
});
