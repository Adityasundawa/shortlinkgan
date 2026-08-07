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
            // Menambahkan foreign key 'campaign_id'
            $table->foreignId('campaign_id')
                  ->nullable() // 'nullable()' jika shortlink boleh tidak memiliki campaign
                  ->constrained('campaigns') // 'campaigns' adalah nama tabel campaign
                  ->onDelete('set null'); // Opsional: jika campaign dihapus, set campaign_id ke NULL
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
       Schema::table('short_links', function (Blueprint $table) {
            // Untuk membatalkan (rollback) migrasi
            $table->dropForeign(['campaign_id']);
            $table->dropColumn('campaign_id');
        });
    }
};
