<?php

use App\Models\AppSetting;
use App\Models\User;
use App\Support\SiteSettings;
use Auth0\Laravel\Middleware\AuthenticatorMiddleware;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->withoutMiddleware(AuthenticatorMiddleware::class);
});

test('home page is available without running an installer', function () {
    $response = $this->get('/');

    $response->assertOk();
    $response->assertSee('One trusted source.');
});

test('authenticated users can update site settings from the admin screen', function () {
    Storage::fake('public');

    $user = User::factory()->create();

    $response = $this->actingAs($user)->put(route('settings.site.update'), [
        'site_name' => 'Warehouse Scanner',
        'site_url' => 'https://scanner.example.com/',
        'scanner_title' => 'Scan with confidence.',
        'scanner_tagline' => 'Track equipment, intake photos, and hand off labels with your own brand.',
        'label_name' => 'Warehouse Label',
        'label_tagline' => 'Scan for the canonical warehouse record.',
        'primary_color' => '#101010',
        'background_color' => '#fefefe',
        'highlight_color' => '#ff4f00',
        'logo' => UploadedFile::fake()->image('brand.png', 200, 80),
    ]);

    $response->assertRedirect(route('settings.site'));
    expect(AppSetting::query()->where('key', 'site_name')->value('value'))->toBe('Warehouse Scanner');
    expect(AppSetting::query()->where('key', 'site_url')->value('value'))->toBe('https://scanner.example.com');
    expect(app(SiteSettings::class)->get('site_name'))->toBe('Warehouse Scanner');
    expect(app(SiteSettings::class)->get('label_name'))->toBe('Warehouse Label');
    expect(app(SiteSettings::class)->get('primary_color'))->toBe('#101010');

    Storage::disk('public')->assertExists(AppSetting::query()->where('key', 'logo_path')->value('value'));
});

test('site colors must be six digit hex values', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->put(route('settings.site.update'), [
        'site_name' => 'Index',
        'site_url' => 'https://index.test',
        'scanner_title' => 'One trusted source.',
        'scanner_tagline' => '',
        'label_name' => 'Asset Label',
        'label_tagline' => '',
        'primary_color' => 'red',
        'background_color' => '#ffffff',
        'highlight_color' => '#ff4f00',
    ]);

    $response->assertSessionHasErrors('primary_color');
});
