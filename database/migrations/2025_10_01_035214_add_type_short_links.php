<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasColumn('short_links', 'type_short_links')) {
            Schema::table('short_links', function (Blueprint $table) {
                $table->enum('type_short_links', ['shortlink', 'bulk'])->default('shortlink');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('short_links', 'type_short_links')) {
            Schema::table('short_links', function (Blueprint $table) {
                $table->dropColumn('type_short_links');
            });
        }
    }
};
