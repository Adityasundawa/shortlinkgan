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
        // Untuk tabel shortlink traffic
        Schema::table('short_link_trafficts', function (Blueprint $table) {
            $table->string('country')->nullable()->after('domain_decentralizes_id');
            $table->string('city')->nullable()->after('country');
            $table->string('device_type')->nullable()->after('city'); // contoh: 'mobile', 'desktop'
        });

        // Untuk tabel microsite traffic
        Schema::table('microsite_link_trafficts', function (Blueprint $table) {
            $table->string('country')->nullable()->after('domain_decentralizes_id');
            $table->string('city')->nullable()->after('country');
            $table->string('device_type')->nullable()->after('city');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('short_link_trafficts', function (Blueprint $table) {
            $table->dropColumn(['country', 'city', 'device_type']);
        });

        Schema::table('microsite_link_trafficts', function (Blueprint $table) {
            $table->dropColumn(['country', 'city', 'device_type']);
        });
    }
};
