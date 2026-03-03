<?php

namespace Database\Factories;

use App\Models\ScannedItem;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\ItemEvent>
 */
class ItemEventFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'scanned_item_id' => ScannedItem::factory(),
            'user_id' => User::factory(),
            'image_path' => null,
            'is_qr_verified' => fake()->boolean(60),
        ];
    }
}
