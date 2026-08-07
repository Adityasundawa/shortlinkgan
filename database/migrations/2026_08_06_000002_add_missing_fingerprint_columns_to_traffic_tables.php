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
        if (! Schema::hasColumn('short_link_trafficts', 'fingerprint_id')) {
            Schema::table('short_link_trafficts', function (Blueprint $table) {
                if (Schema::hasColumn('short_link_trafficts', 'device_type')) {
                    $table->string('fingerprint_id')->nullable()->after('device_type')->index();

                    return;
                }

                $table->string('fingerprint_id')->nullable()->index();
            });
        }

        if (! Schema::hasColumn('microsite_link_trafficts', 'fingerprint_id')) {
            Schema::table('microsite_link_trafficts', function (Blueprint $table) {
                if (Schema::hasColumn('microsite_link_trafficts', 'device_type')) {
                    $table->string('fingerprint_id')->nullable()->after('device_type')->index();

                    return;
                }

                $table->string('fingerprint_id')->nullable()->index();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('short_link_trafficts', function (Blueprint $table) {
            if (Schema::hasColumn('short_link_trafficts', 'fingerprint_id')) {
                $table->dropColumn('fingerprint_id');
            }
        });

        Schema::table('microsite_link_trafficts', function (Blueprint $table) {
            if (Schema::hasColumn('microsite_link_trafficts', 'fingerprint_id')) {
                $table->dropColumn('fingerprint_id');
            }
        });
    }
};
