<?php

use App\Models\Item;
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
        'is_qr_verified' => true,
    ]);

    $response = $this->get('/'.$item->uuid);

    $response->assertOk();
    $response->assertSee('Temporary Label');
    $response->assertSee(route('items.show', ['uuid' => $item->uuid], true));
    $response->assertSee('Timeline');
    $response->assertSee('Image attached. Log in for full-resolution timeline images.');
    $response->assertDontSee('Add Photo Event');
});
