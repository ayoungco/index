<?php

use App\Models\ScannedItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;

uses(RefreshDatabase::class);

test('guest sees login prompt for unknown scanned uuid', function () {
    $uuid = (string) Str::uuid();

    $response = $this->get('/'.$uuid);

    $response->assertOk();
    $response->assertSee('Object Not Initialized');
    $response->assertSee('Login With Auth0');
});

test('guest sees condensed timeline without inline images', function () {
    $item = ScannedItem::factory()->create();
    $item->events()->create([
        'user_id' => $item->user_id,
        'image_path' => 'scanned-items/'.$item->uuid.'/photo.jpg',
        'is_qr_verified' => true,
    ]);

    $response = $this->get('/'.$item->uuid);

    $response->assertOk();
    $response->assertSee('Timeline');
    $response->assertSee('Image attached. Log in for full-resolution timeline images.');
    $response->assertDontSee('Add Photo Event');
});
