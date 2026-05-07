<?php

use App\Models\Item;
use App\Models\ItemAccess;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;

uses(RefreshDatabase::class);

beforeEach(function () {
    installApplication();
});

test('guest sees login prompt for unknown uuid', function () {
    $uuid = (string) Str::uuid();
    $expectedReturnTo = route('items.show', ['uuid' => $uuid], true);

    $response = $this->get('/'.$uuid);

    $response->assertOk();
    $response->assertSee('Object Not Initialized');
    $response->assertSee('Login With Auth0');
    $response->assertSee(route('login', ['returnTo' => $expectedReturnTo], true), false);
    $response->assertSessionHas('auth.scanned_item_url', $expectedReturnTo);
});

test('guest sees condensed timeline without inline images', function () {
    $item = Item::factory()->create();
    $item->events()->create([
        'user_id' => $item->user_id,
        'image_path' => 'items/'.$item->uuid.'/photo.jpg',
        'comment' => 'Shelf check complete.',
        'is_qr_verified' => true,
    ]);

    $response = $this->get('/'.$item->uuid);

    $response->assertOk();
    $response->assertSee($item->name);
    $response->assertSee(route('items.show', ['uuid' => $item->uuid], true));
    $response->assertSee('Timeline');
    $response->assertSee('Shelf check complete.');
    $response->assertSee('Image attached. Log in for full-resolution timeline images.');
    $response->assertDontSee('Add Photo Event');
});

test('guest item access is recorded as anonymous timeline activity', function () {
    $item = Item::factory()->create();

    $response = $this
        ->withHeader('User-Agent', 'Mozilla/5.0 Version/17.0 Mobile/15E148 Safari/604.1')
        ->withHeader('CF-IPCity', 'New%20York')
        ->withHeader('CF-IPCountry', 'US')
        ->withHeader('CloudFront-Viewer-Country-Name', 'United%20States')
        ->get('/'.$item->uuid);

    $response->assertOk();
    $response->assertSee('Object accessed');
    $response->assertSee('anonymous user from New York, United States using Safari');

    $this->assertDatabaseHas('item_accesses', [
        'item_id' => $item->id,
        'user_id' => null,
        'city' => 'New York',
        'country' => 'United States',
        'country_code' => 'US',
        'browser' => 'Safari',
    ]);
});

test('authenticated item access is recorded on the timeline', function () {
    $item = Item::factory()->create();
    $user = $item->creator;

    $this->actingAs($user, 'auth0-session');

    $response = $this
        ->withHeader('User-Agent', 'Mozilla/5.0 Chrome/124.0.0.0 Safari/537.36')
        ->get('/'.$item->uuid);

    $response->assertOk();
    $response->assertSee('Object accessed');
    $response->assertSee($user->name);

    expect(ItemAccess::query()->where('item_id', $item->id)->where('user_id', $user->id)->exists())->toBeTrue();
});
