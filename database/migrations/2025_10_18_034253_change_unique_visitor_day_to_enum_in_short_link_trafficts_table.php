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
        Schema::table('short_link_trafficts', function (Blueprint $table) {
            // Mengubah tipe kolom menjadi ENUM dengan nilai 'yes' atau 'no'

            $table->enum('unique_visitor_day', ['yes', 'no'])->default('no')->change();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('short_link_trafficts', function (Blueprint $table) {
            // Mengembalikan kolom ke tipe integer jika di-rollback
            $table->integer('unique_visitor_day')->change();
        });
    }
};
