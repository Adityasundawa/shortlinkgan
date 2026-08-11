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
        Schema::table('short_links', function (Blueprint $table) {
            if (! Schema::hasColumn('short_links', 'images_background')) {
                $table->string('images_background')->nullable();
            }

            if (! Schema::hasColumn('short_links', 'custom_title')) {
                $table->string('custom_title')->nullable();
            }

            if (! Schema::hasColumn('short_links', 'custom_description')) {
                $table->string('custom_description')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('short_links', function (Blueprint $table) {
            foreach (['images_background', 'custom_title', 'custom_description'] as $column) {
                if (Schema::hasColumn('short_links', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
