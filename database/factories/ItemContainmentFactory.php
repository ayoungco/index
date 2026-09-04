<?php

namespace Database\Factories;

use App\Models\Item;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\ItemContainment>
 */
class ItemContainmentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'container_item_id' => Item::factory(),
            'contained_item_id' => Item::factory(),
            'evidence_event_id' => null,
            'created_by' => User::factory(),
            'quantity' => null,
            'unit' => null,
            'position' => null,
            'observed_at' => now(),
            'removed_at' => null,
        ];
    }
}
