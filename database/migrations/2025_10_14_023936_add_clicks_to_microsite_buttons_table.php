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
        Schema::table('microsite_link_buttons', function (Blueprint $table) {
            // Add a column to count clicks, defaulting to 0
            $table->unsignedBigInteger('clicks')->default(0)->after('url');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('microsite_link_buttons', function (Blueprint $table) {
            $table->dropColumn('clicks');
        });
    }
};
