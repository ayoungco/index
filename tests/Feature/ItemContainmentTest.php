<?php

use App\Models\Item;
use App\Models\ItemContainment;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    installApplication();
});

test('items can represent active and historical containment edges', function () {
    $crate = Item::factory()->create([
        'name' => 'Receiving crate 12',
        'operational_role' => 'holding_unit',
    ]);
    $part = Item::factory()->create([
        'name' => 'Hydraulic valve',
        'operational_role' => 'product',
    ]);

    ItemContainment::factory()->create([
        'container_item_id' => $crate->id,
        'contained_item_id' => $part->id,
        'quantity' => 4,
        'unit' => 'each',
    ]);

    expect($crate->containedItems)->toHaveCount(1);
    expect($crate->containedItems->first()->is($part))->toBeTrue();
    expect($part->containers)->toHaveCount(1);
    expect((float) $crate->containedItems->first()->pivot->quantity)->toBe(4.0);

    ItemContainment::query()->update(['removed_at' => now()]);

    expect($crate->fresh()->containedItems)->toHaveCount(0);
    expect($part->fresh()->containers)->toHaveCount(0);
    expect(ItemContainment::query()->whereNotNull('removed_at')->count())->toBe(1);
});
