<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('short_links', function (Blueprint $table) {
            $table->boolean('use_play_button')->default(true)->after('images_background');
        });

        Schema::table('microsite_links', function (Blueprint $table) {
            $table->boolean('use_play_button')->default(true)->after('images_background');
        });
    }

    public function down(): void
    {
        Schema::table('short_links', function (Blueprint $table) {
            $table->dropColumn('use_play_button');
        });

        Schema::table('microsite_links', function (Blueprint $table) {
            $table->dropColumn('use_play_button');
        });
    }
};
