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
            $table->string('utm_campaign')->nullable()->after('original_url');
            $table->string('utm_medium')->nullable()->after('utm_campaign');
            $table->string('utm_source')->nullable()->after('utm_medium');
            $table->string('utm_content')->nullable()->after('utm_source');
            $table->string('utm_term')->nullable()->after('utm_content');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('short_links', function (Blueprint $table) {
            $table->dropColumn('utm_campaign');
            $table->dropColumn('utm_medium');
            $table->dropColumn('utm_source');
            $table->dropColumn('utm_content');
            $table->dropColumn('utm_term');
        });
    }
};
