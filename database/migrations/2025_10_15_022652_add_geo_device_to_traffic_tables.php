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
        $this->addTrackingColumns('short_link_trafficts');
        $this->addTrackingColumns('microsite_link_trafficts');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $this->dropTrackingColumns('short_link_trafficts');
        $this->dropTrackingColumns('microsite_link_trafficts');
    }

    private function addTrackingColumns(string $table): void
    {
        Schema::table($table, function (Blueprint $tableBlueprint) use ($table) {
            if (! Schema::hasColumn($table, 'country')) {
                $tableBlueprint->string('country')->nullable()->after('domain_decentralizes_id');
            }

            if (! Schema::hasColumn($table, 'city')) {
                $tableBlueprint->string('city')->nullable()->after('country');
            }

            if (! Schema::hasColumn($table, 'device_type')) {
                $tableBlueprint->string('device_type')->nullable()->after('city');
            }
        });
    }

    private function dropTrackingColumns(string $table): void
    {
        Schema::table($table, function (Blueprint $tableBlueprint) use ($table) {
            foreach (['country', 'city', 'device_type'] as $column) {
                if (Schema::hasColumn($table, $column)) {
                    $tableBlueprint->dropColumn($column);
                }
            }
        });
    }
};
