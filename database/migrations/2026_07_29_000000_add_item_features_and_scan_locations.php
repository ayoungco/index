<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('items', function (Blueprint $table): void {
            $table->foreignId('featured_event_id')->nullable()->after('user_id')->constrained('item_events')->nullOnDelete();
        });

        Schema::table('item_accesses', function (Blueprint $table): void {
            $table->decimal('latitude', 10, 7)->nullable()->after('country_code');
            $table->decimal('longitude', 10, 7)->nullable()->after('latitude');
            $table->string('address', 512)->nullable()->after('longitude');
            $table->string('building')->nullable()->after('address');
            $table->string('room')->nullable()->after('building');
            $table->string('container')->nullable()->after('room');
        });
    }

    public function down(): void
    {
        Schema::table('items', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('featured_event_id');
        });

        Schema::table('item_accesses', function (Blueprint $table): void {
            $table->dropColumn(['latitude', 'longitude', 'address', 'building', 'room', 'container']);
        });
    }
};
