<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $this->addUtmColumns('short_link_trafficts');
        $this->addUtmColumns('microsite_link_trafficts');
    }

    public function down(): void
    {
        $this->dropUtmColumns('short_link_trafficts');
        $this->dropUtmColumns('microsite_link_trafficts');
    }

    private function addUtmColumns(string $table): void
    {
        Schema::table($table, function (Blueprint $tableBlueprint) use ($table) {
            $afterColumn = Schema::hasColumn($table, 'fingerprint_id') ? 'fingerprint_id' : null;

            foreach (['utm_campaign', 'utm_medium', 'utm_source', 'utm_content', 'utm_term'] as $column) {
                if (Schema::hasColumn($table, $column)) {
                    continue;
                }

                $definition = $tableBlueprint->string($column)->nullable()->index();
                if ($afterColumn) {
                    $definition->after($afterColumn);
                    $afterColumn = $column;
                }
            }
        });
    }

    private function dropUtmColumns(string $table): void
    {
        Schema::table($table, function (Blueprint $tableBlueprint) use ($table) {
            foreach (['utm_campaign', 'utm_medium', 'utm_source', 'utm_content', 'utm_term'] as $column) {
                if (Schema::hasColumn($table, $column)) {
                    $tableBlueprint->dropColumn($column);
                }
            }
        });
    }
};
