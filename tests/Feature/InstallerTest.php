<?php

use App\Models\AppSetting;
use App\Models\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

test('first run requests are redirected to the installer', function () {
    $response = $this->get('/');

    $response->assertRedirect(route('install.show'));
});

test('installer stores branding settings and applies them to scanner pages and labels', function () {
    Storage::fake('public');

    $response = $this->post(route('install.store'), [
        'site_name' => 'Warehouse Scanner',
        'site_url' => 'https://scanner.example.com/',
        'scanner_title' => 'Scan with confidence.',
        'scanner_tagline' => 'Track equipment, intake photos, and hand off labels with your own brand.',
        'label_name' => 'Warehouse Label',
        'label_tagline' => 'Scan for the canonical warehouse record.',
        'logo' => UploadedFile::fake()->image('brand.png', 200, 80),
    ]);

    $response->assertRedirect(route('home'));
    expect(AppSetting::query()->where('key', 'installed_at')->exists())->toBeTrue();
    expect(AppSetting::query()->where('key', 'site_url')->value('value'))->toBe('https://scanner.example.com');

    $home = $this->get('/');
    $home->assertOk();
    $home->assertSee('Warehouse Scanner');
    $home->assertSee('Scan with confidence.');

    $user = User::factory()->create();
    $item = Item::factory()->for($user)->create([
        'description' => null,
    ]);

    $label = $this->actingAs($user)->get(route('items.print', ['uuid' => $item->uuid]));
    $label->assertOk();
    $label->assertSee('Warehouse Label');
    $label->assertSee('Scan for the canonical warehouse record.');

    Storage::disk('public')->assertExists(AppSetting::query()->where('key', 'logo_path')->value('value'));
});
