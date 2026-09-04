<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('items', function (Blueprint $table): void {
            $table->string('operational_role', 32)->nullable()->after('type_namespace');
        });

        Schema::create('item_containments', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('container_item_id')->constrained('items')->cascadeOnDelete();
            $table->foreignId('contained_item_id')->constrained('items')->cascadeOnDelete();
            $table->foreignId('evidence_event_id')->nullable()->constrained('item_events')->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->decimal('quantity', 14, 3)->nullable();
            $table->string('unit', 32)->nullable();
            $table->string('position', 120)->nullable();
            $table->timestamp('observed_at')->nullable();
            $table->timestamp('removed_at')->nullable();
            $table->timestamps();

            $table->index(['container_item_id', 'removed_at']);
            $table->index(['contained_item_id', 'removed_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('item_containments');

        Schema::table('items', function (Blueprint $table): void {
            $table->dropColumn('operational_role');
        });
    }
};
