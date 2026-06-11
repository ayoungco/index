<?php

use App\Models\Item;
use App\Models\ItemAccess;
use App\Models\User;
use App\Services\ImageCompressionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
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

test('guest sees timeline thumbnails and latest image on label', function () {
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
    $response->assertSee(Storage::disk('public')->url('items/'.$item->uuid.'/photo.jpg'), false);
    $response->assertSee('Latest image for '.$item->name);
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

test('dashboard exposes a create from photo workflow to verified users', function () {
    $user = User::factory()->create();

    $this->actingAs($user, 'auth0-session');

    $response = $this->get('/dashboard');

    $response->assertOk();
    $response->assertSee('Create From Photo');
    $response->assertSee('Create Item From Photo');
    $response->assertSee(route('items.from-photo.store'), false);
});

test('verified users can create a new item from only a photo', function () {
    Storage::fake('public');

    $user = User::factory()->create();

    $this->mock(ImageCompressionService::class, function ($mock) {
        $mock->shouldReceive('compressAndStore')
            ->once()
            ->andReturnUsing(function (UploadedFile $_photo, string $uuid): string {
                $path = 'items/'.$uuid.'/photo.jpg';
                Storage::disk('public')->put($path, 'compressed-photo');

                return $path;
            });
    });

    $this->actingAs($user, 'auth0-session');

    $jpeg = base64_decode(implode('', [
        '/9j/4AAQSkZJRgABAQAAAQABAAD/2wBDAP//////////////////////////////',
        '////////////////////////////////////////////////////////2wBDAf//////',
        '////////////////////////////////////////////////////////////wAARCAAB',
        'AAEDASIAAhEBAxEB/8QAFQABAQAAAAAAAAAAAAAAAAAAAAX/xAAUEAEAAAAAAAAA',
        'AAAAAAAAAAAA/9oADAMBAAIQAxAAAAH/xAAUEAEAAAAAAAAAAAAAAAAAAAAA/9oA',
        'CAEBAAEFAqf/xAAUEQEAAAAAAAAAAAAAAAAAAAAA/9oACAEDAQE/Aaf/xAAUEQEA',
        'AAAAAAAAAAAAAAAAAAAA/9oACAECAQE/Aaf/xAAUEAEAAAAAAAAAAAAAAAAAAAAA',
        '/9oACAEBAAY/Aqf/xAAUEAEAAAAAAAAAAAAAAAAAAAAA/9oACAEBAAE/IX//2gAM',
        'AwEAAgADAAAAEP/EABQRAQAAAAAAAAAAAAAAAAAAABD/2gAIAQMBAT8QP//EABQR',
        'AQAAAAAAAAAAAAAAAAAAABD/2gAIAQIBAT8QP//EABQQAQAAAAAAAAAAAAAAAAAA',
        'ABD/2gAIAQEAAT8QP//Z',
    ]));

    $response = $this->post(route('items.from-photo.store'), [
        'photo' => UploadedFile::fake()->createWithContent('new-item.jpg', $jpeg),
    ]);

    $item = Item::query()->sole();

    $response->assertRedirect(route('items.show', ['uuid' => $item->uuid]));
    expect($item->name)->toStartWith('Photo item ');
    expect($item->description)->toBeNull();
    expect($item->creator->is($user))->toBeTrue();

    $event = $item->events()->sole();
    expect($event->image_path)->toBe('items/'.$item->uuid.'/photo.jpg');
    expect($event->comment)->toBeNull();
    expect($event->is_qr_verified)->toBeFalse();
    Storage::disk('public')->assertExists($event->image_path);
});

test('the image processor writes a generated jpeg to the public disk', function () {
    Storage::fake('public');

    $uuid = (string) Str::uuid();
    $photo = UploadedFile::fake()->image('source.png', 1200, 800);

    $path = app(ImageCompressionService::class)->compressAndStore($photo, $uuid);

    expect($path)->toStartWith('items/'.$uuid.'/')->toEndWith('.jpg');
    Storage::disk('public')->assertExists($path);
    expect(Storage::disk('public')->size($path))->toBeGreaterThan(0);
});

test('the original-image fallback always stores an explicit extension', function () {
    Storage::fake('public');

    $item = Item::factory()->create();
    $user = $item->creator;

    $this->mock(ImageCompressionService::class, function ($mock) {
        $mock->shouldReceive('compressAndStore')
            ->once()
            ->andThrow(new RuntimeException('Decoder unavailable.'));
    });

    $this->actingAs($user, 'auth0-session');

    $response = $this->post(route('items.events.store', ['uuid' => $item->uuid]), [
        'photo' => UploadedFile::fake()->image('fallback.jpeg'),
    ]);

    $event = $item->events()->sole();

    $response->assertRedirect(route('items.show', ['uuid' => $item->uuid]));
    expect($event->image_path)->toMatch('/^items\/'.$item->uuid.'\/[0-9a-f-]+\.jpg$/');
    Storage::disk('public')->assertExists($event->image_path);
});

test('oversized timeline photos return a visible validation error', function () {
    $item = Item::factory()->create();
    $user = $item->creator;

    $this->actingAs($user, 'auth0-session');

    $response = $this
        ->from(route('items.show', ['uuid' => $item->uuid]))
        ->post(route('items.events.store', ['uuid' => $item->uuid]), [
            'photo' => UploadedFile::fake()->create('large.jpg', 3072, 'image/jpeg'),
        ]);

    $response->assertRedirect(route('items.show', ['uuid' => $item->uuid]));
    $response->assertSessionHasErrors('photo');
    expect($item->events()->count())->toBe(0);
});

test('requests over the PHP post limit return a visible upload error', function () {
    $item = Item::factory()->create();
    $user = $item->creator;

    $this->actingAs($user, 'auth0-session');

    $response = $this
        ->from(route('items.show', ['uuid' => $item->uuid]))
        ->withServerVariables(['CONTENT_LENGTH' => 9 * 1024 * 1024])
        ->post(route('items.events.store', ['uuid' => $item->uuid]));

    $response->assertRedirect(route('items.show', ['uuid' => $item->uuid]));
    $response->assertSessionHas('status', 'Upload rejected because the request was larger than the server allows. Choose a smaller photo and try again.');
    $response->assertSessionHas('statusType', 'critical');
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
