<?php

use App\Models\Item;
use App\Models\ItemAccess;
use App\Models\User;
use App\Services\ImageCompressionService;
use App\Services\ReverseGeocoder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

uses(RefreshDatabase::class);

beforeEach(function () {
    installApplication();
});

test('guests are redirected to Auth0 before viewing an item', function () {
    $uuid = (string) Str::uuid();
    $response = $this->get('/'.$uuid);

    $response->assertRedirect(route('login'));
});

test('the scanned object claim flow exposes optional Wikidata concept search', function () {
    $user = User::factory()->create();
    $uuid = (string) Str::uuid();

    $this->actingAs($user, 'auth0-session');

    $response = $this->get('/'.$uuid);

    $response->assertOk();
    $response->assertSee('Classify with Wikidata');
    $response->assertSee('data-wikidata-picker', false);
    $response->assertSee(route('wikidata.search'), false);
    $response->assertSee('name="wikidata_qid"', false);
});

test('verified users can register a scanned object with a Wikidata concept', function () {
    Storage::fake('uploads');

    $user = User::factory()->create();
    $uuid = (string) Str::uuid();

    $this->mock(ImageCompressionService::class, function ($mock) {
        $mock->shouldReceive('compressAndStore')
            ->once()
            ->andReturn('items/example/photo.jpg');
    });

    $this->actingAs($user, 'auth0-session');

    $response = $this->post(route('items.initialize', ['uuid' => $uuid]), [
        'name' => 'Oxygen tank',
        'wikidata_qid' => 'Q629',
        'photo' => UploadedFile::fake()->image('oxygen-tank.jpg'),
    ]);

    $response->assertRedirect(route('items.show', ['uuid' => $uuid]));
    expect(Item::query()->where('uuid', $uuid)->value('wikidata_qid'))->toBe('Q629');
});

test('authenticated users see timeline images behind an on-demand disclosure', function () {
    $item = Item::factory()->create();
    $this->actingAs($item->creator, 'auth0-session');
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
    $response->assertSee('Show image');
    $response->assertSee('data-src="'.route('media.show', ['path' => 'items/'.$item->uuid.'/photo.jpg']).'"', false);
    expect(preg_match('/<details[^>]*data-timeline-image.*?<img\s+src=/s', $response->getContent()))->toBe(0);
    $response->assertSee('Latest image for '.$item->name);
    $response->assertDontSee('Add Photo Event');
});

test('authenticated item access is recorded for the signed-in user', function () {
    $item = Item::factory()->create();
    $this->actingAs($item->creator, 'auth0-session');

    $response = $this
        ->withHeader('User-Agent', 'Mozilla/5.0 Version/17.0 Mobile/15E148 Safari/604.1')
        ->withHeader('CF-IPCity', 'New%20York')
        ->withHeader('CF-IPCountry', 'US')
        ->withHeader('CloudFront-Viewer-Country-Name', 'United%20States')
        ->get('/'.$item->uuid);

    $response->assertOk();
    $response->assertSee('Object accessed');
    $response->assertSeeInOrder([
        '<td class="compose-log__rail-cell"',
        '<time class="compose-log__time"',
        '<span class="compose-log__source">'.$item->creator->displayLabel().' · 1 scans</span>',
        '<span class="compose-log__message">Object accessed | from New York, United States using Safari</span>',
    ], false);
    $response->assertSeeInOrder([
        '<span class="compose-log__source">'.$item->creator->displayLabel().' · 1 scans</span>',
        'Object accessed | from New York, United States using Safari',
    ], false);

    $this->assertDatabaseHas('item_accesses', [
        'item_id' => $item->id,
        'user_id' => $item->creator->id,
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
    $response->assertSee('app-quick-create', false);
    $response->assertSee('Camera');
    $response->assertSee('Create');
    $response->assertDontSee('Initial note');
    $response->assertSee(route('items.from-photo.store'), false);
    $response->assertDontSee('Wikidata QID');
    $response->assertSee('app-brand__mark', false);
    $response->assertSee('app-logo-mark', false);
    $response->assertSee('app-header__search', false);
    $response->assertSee('Search assets');
    $response->assertDontSee('Rapid Full Text Search');
    $response->assertSee(parse_url(config('app.url'), PHP_URL_HOST));
    $response->assertDontSee('>Things<', false);
    $response->assertDontSee('>Properties<', false);
    $response->assertDontSee('>Relations<', false);
    $response->assertDontSee('>Messages<', false);
});

test('dashboard search filters objects from the shared header endpoint', function () {
    $user = User::factory()->create();
    Item::factory()->create([
        'name' => 'Scanner cradle',
        'description' => 'Receiving desk asset',
        'user_id' => $user->id,
    ]);
    Item::factory()->create([
        'name' => 'Warehouse cart',
        'description' => 'Back room transport',
        'user_id' => $user->id,
    ]);

    $this->actingAs($user, 'auth0-session');

    $response = $this->get(route('dashboard', ['q' => 'scanner']));

    $response->assertOk();
    $response->assertSee('Search Results');
    $response->assertSee('value="scanner"', false);
    $response->assertSee('Scanner cradle');
    $response->assertDontSee('Warehouse cart');
    $response->assertSee('Clear search');
});

test('dashboard ajax search returns compact object matches', function () {
    $user = User::factory()->create();
    $scanner = Item::factory()->create([
        'name' => 'Scanner cradle',
        'description' => 'Receiving desk asset',
        'user_id' => $user->id,
    ]);
    Item::factory()->create([
        'name' => 'Warehouse cart',
        'description' => 'Back room transport',
        'user_id' => $user->id,
    ]);

    $this->actingAs($user, 'auth0-session');

    $response = $this->getJson(route('dashboard.search', ['q' => 'scanner']));

    $response
        ->assertOk()
        ->assertJsonCount(1, 'results')
        ->assertJsonPath('results.0.name', 'Scanner cradle')
        ->assertJsonPath('results.0.description', 'Receiving desk asset')
        ->assertJsonPath('results.0.type', 'Unclassified asset')
        ->assertJsonPath('results.0.url', route('items.show', ['uuid' => $scanner->uuid]));
});

test('verified users can create a new item from only a photo', function () {
    Storage::fake('uploads');

    $user = User::factory()->create();

    $this->mock(ImageCompressionService::class, function ($mock) {
        $mock->shouldReceive('compressAndStore')
            ->once()
            ->andReturnUsing(function (UploadedFile $_photo, string $uuid): string {
                $path = 'items/'.$uuid.'/photo.jpg';
                Storage::disk('uploads')->put($path, 'compressed-photo');

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
    expect($item->name)->toBe('new-item');
    expect($item->description)->toBeNull();
    expect($item->creator->is($user))->toBeTrue();

    $event = $item->events()->sole();
    expect($event->image_path)->toBe('items/'.$item->uuid.'/photo.jpg');
    expect($event->comment)->toBeNull();
    expect($event->is_qr_verified)->toBeFalse();
    Storage::disk('uploads')->assertExists($event->image_path);
});

test('scanned uuid initialization uses the photo filename when name is blank', function () {
    Storage::fake('uploads');

    $user = User::factory()->create();
    $uuid = (string) Str::uuid();

    $this->mock(ImageCompressionService::class, function ($mock) {
        $mock->shouldReceive('compressAndStore')
            ->once()
            ->andReturn('items/example/photo.jpg');
    });

    $this->actingAs($user, 'auth0-session');

    $response = $this->post(route('items.initialize', ['uuid' => $uuid]), [
        'photo' => UploadedFile::fake()->image('warehouse-camera.png'),
    ]);

    $response->assertRedirect(route('items.show', ['uuid' => $uuid]));
    expect(Item::query()->where('uuid', $uuid)->value('name'))->toBe('warehouse-camera');
});

test('verified users can update item description from the item page', function () {
    $item = Item::factory()->create([
        'description' => null,
    ]);
    $user = $item->creator;

    $this->actingAs($user, 'auth0-session');

    $show = $this->get(route('items.show', ['uuid' => $item->uuid]));

    $show->assertOk();
    $show->assertSee(route('items.update', ['uuid' => $item->uuid]), false);
    $show->assertSee('Description');

    $response = $this->patch(route('items.update', ['uuid' => $item->uuid]), [
        'description' => 'Stored in bay 4 for repair intake.',
    ]);

    $response->assertRedirect(route('items.show', ['uuid' => $item->uuid]));
    $response->assertSessionHas('status', 'Object description updated.');
    expect($item->refresh()->description)->toBe('Stored in bay 4 for repair intake.');
});

test('print label offers detailed and compact layouts', function () {
    $item = Item::factory()->create();

    $this->actingAs($item->creator, 'auth0-session');

    $vertical = $this->get(route('items.print', ['uuid' => $item->uuid, 'layout' => 'vertical']));

    $vertical->assertOk();
    $vertical->assertSee('index-min-label--vertical', false);
    $vertical->assertSee('index-min-label__logo-mark', false);
    $vertical->assertDontSee(asset('index-v.svg'), false);
    $vertical->assertDontSee('<span class="index-min-label__placeholder"', false);
    $vertical->assertSeeInOrder([
        'index-min-label__logo',
        'index-min-label__qr',
        'index-min-label__identity',
        'index-min-label__title',
        'index-min-label__subtitle',
    ], false);

    $horizontal = $this->get(route('items.print', ['uuid' => $item->uuid, 'layout' => 'horizontal']));

    $horizontal->assertOk();
    $horizontal->assertSee('index-min-label--horizontal', false);
    $horizontal->assertSee('index-min-label__placeholder', false);
    $horizontal->assertSeeInOrder([
        'index-min-label__qr',
        'index-min-label__logo',
        'index-min-label__latest',
        'index-min-label__identity',
        'index-min-label__title',
        'index-min-label__subtitle',
    ], false);

    $compact = $this->get(route('items.print', ['uuid' => $item->uuid, 'layout' => 'compact']));

    $compact->assertOk();
    $compact->assertSee('index-min-label--compact', false);
    $compact->assertSee('size: 2.25in 2.75in portrait', false);
    $compact->assertSeeInOrder([
        'index-min-label__qr',
        'index-min-label__identity',
        'index-min-label__title',
    ], false);
    $compact->assertDontSee('<span class="index-min-label__logo">', false);
    $compact->assertDontSee('<div class="index-min-label__subtitle">', false);

    $qr = $this->get(route('items.print', ['uuid' => $item->uuid, 'layout' => 'qr']));

    $qr->assertOk();
    $qr->assertSee('index-min-label--qr', false);
    $qr->assertSee('size: 2in 2.25in portrait', false);
    $qr->assertSee('Scan me');
    $qr->assertDontSee('<div class="index-min-label__identity">', false);
});

test('item type is shown beneath the item title', function () {
    $item = Item::factory()->create([
        'type_namespace' => 'medical-device',
    ]);

    $this->actingAs($item->creator, 'auth0-session');

    $response = $this->get('/'.$item->uuid);

    $response->assertOk();
    $response->assertSee('index-min-label__subtitle', false);
    $response->assertSee('Medical Device');
});

test('the image processor writes a generated jpeg to the private upload disk', function () {
    Storage::fake('uploads');

    $uuid = (string) Str::uuid();
    $photo = UploadedFile::fake()->image('source.png', 1200, 800);

    $path = app(ImageCompressionService::class)->compressAndStore($photo, $uuid);

    expect($path)->toStartWith('items/'.$uuid.'/')->toEndWith('.jpg');
    Storage::disk('uploads')->assertExists($path);
    expect(Storage::disk('uploads')->size($path))->toBeGreaterThan(0);
});

test('a user can select a timeline image as the featured photo', function () {
    $item = Item::factory()->create();
    $event = $item->events()->create([
        'user_id' => $item->user_id,
        'image_path' => 'items/'.$item->uuid.'/featured.jpg',
        'is_qr_verified' => false,
    ]);

    $this->actingAs($item->creator, 'auth0-session')
        ->post(route('items.featured-photo.update', ['uuid' => $item->uuid, 'event' => $event->id]))
        ->assertRedirect(route('items.show', ['uuid' => $item->uuid]));

    expect($item->fresh()->featured_event_id)->toBe($event->id);
});

test('a scan location is reverse geocoded and retained with room context', function () {
    $item = Item::factory()->create();
    $this->mock(ReverseGeocoder::class, function ($mock) {
        $mock->shouldReceive('lookup')->once()->andReturn([
            'address' => '100 Main Street, Beacon, New York',
            'city' => 'Beacon',
            'country' => 'United States',
            'country_code' => 'US',
            'building' => 'Receiving Building',
        ]);
    });

    $this->actingAs($item->creator, 'auth0-session')
        ->post(route('items.location.store', ['uuid' => $item->uuid]), [
            'latitude' => 41.5044,
            'longitude' => -73.9696,
            'room' => 'Dock 2',
            'container' => 'Shelf A',
        ])
        ->assertRedirect(route('items.show', ['uuid' => $item->uuid]));

    $this->assertDatabaseHas('item_accesses', [
        'item_id' => $item->id,
        'city' => 'Beacon',
        'building' => 'Receiving Building',
        'room' => 'Dock 2',
        'container' => 'Shelf A',
    ]);
});

test('snake case identifiers resolve to the matching item slug', function () {
    $item = Item::factory()->create(['slug' => 'oxygen-tank-shelf-3']);

    $this->actingAs($item->creator, 'auth0-session')
        ->get('/oxygen_tank_shelf_3')
        ->assertOk()
        ->assertSee($item->name);
});

test('a photo is rejected when it cannot be decoded and re-encoded safely', function () {
    Storage::fake('uploads');

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

    $response->assertRedirect(route('items.show', ['uuid' => $item->uuid]));
    $response->assertSessionHas('status', 'Upload failed due to a temporary connection issue. Please try again.');
    expect($item->events()->count())->toBe(0);
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

test('timeline uses email when an actor has no display name', function () {
    $item = Item::factory()->create();
    $user = User::factory()->create([
        'name' => '',
        'email' => 'operator@example.com',
    ]);

    $item->events()->create([
        'user_id' => $user->id,
        'image_path' => 'items/'.$item->uuid.'/photo.jpg',
        'is_qr_verified' => false,
    ]);

    $this->actingAs($user, 'auth0-session');

    $response = $this->get('/'.$item->uuid);

    $response->assertOk();
    $response->assertSee('<span class="compose-log__source">operator@example.com · 1 scans</span>', false);
    $response->assertSee('app-header__search', false);
    $response->assertSee(route('items.print', ['uuid' => $item->uuid, 'layout' => 'vertical']), false);
    $response->assertSee(route('items.print', ['uuid' => $item->uuid, 'layout' => 'horizontal']), false);
    $response->assertSee(route('items.print', ['uuid' => $item->uuid, 'layout' => 'compact']), false);
    $response->assertSee(route('items.print', ['uuid' => $item->uuid, 'layout' => 'qr']), false);
});
