<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasColumn('microsite_pop_unders', 'precentage')) {
            DB::statement('ALTER TABLE microsite_pop_unders MODIFY precentage INT NULL DEFAULT 0');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('microsite_pop_unders', 'precentage')) {
            DB::statement('ALTER TABLE microsite_pop_unders MODIFY precentage INT NOT NULL');
        }
    }
};
