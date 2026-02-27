<?php

namespace Database\Seeders;

use App\Models\Item;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ScannedItemSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::query()->first() ?? User::factory()->create([
            'email_verified_at' => now(),
        ]);

        foreach (range(1, 10) as $index) {
            Item::query()->create([
                'uuid' => (string) Str::uuid(),
                'name' => 'Seeded Item '.$index,
                'description' => 'Dummy scanned object for testing QR lookup flows.',
                'user_id' => $user->id,
            ]);
        }
    }
}
