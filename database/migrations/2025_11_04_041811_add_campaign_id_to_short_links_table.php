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
        if (! Schema::hasColumn('short_links', 'campaign_id')) {
            Schema::table('short_links', function (Blueprint $table) {
                $table->foreignId('campaign_id')
                    ->nullable()
                    ->constrained('campaigns')
                    ->onDelete('set null');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('short_links', 'campaign_id')) {
            Schema::table('short_links', function (Blueprint $table) {
                try {
                    $table->dropForeign(['campaign_id']);
                } catch (Throwable) {
                    //
                }

                $table->dropColumn('campaign_id');
            });
        }
    }
};
