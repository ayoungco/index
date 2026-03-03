<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        // Deprecated by 2026_03_03_000110_create_item_events_table.php.
        // Keep this migration file for history, but no-op to avoid duplicate table creation.
    }

    public function down(): void
    {
        // No-op; canonical rollback is handled by the replacement migration.
    }
};
