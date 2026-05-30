<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('items', function (Blueprint $table) {
            $table->string('slug')->nullable()->after('name');
            $table->string('wikidata_qid', 32)->nullable()->after('slug');
            $table->string('type_namespace', 64)->nullable()->after('wikidata_qid');

            $table->index('wikidata_qid');
            $table->unique(['type_namespace', 'slug']);
        });
    }

    public function down(): void
    {
        Schema::table('items', function (Blueprint $table) {
            $table->dropUnique(['type_namespace', 'slug']);
            $table->dropIndex(['wikidata_qid']);
            $table->dropColumn(['slug', 'wikidata_qid', 'type_namespace']);
        });
    }
};
