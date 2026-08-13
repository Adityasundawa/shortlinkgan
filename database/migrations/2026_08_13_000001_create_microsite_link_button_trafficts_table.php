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
        // Migration sebelumnya bisa gagal di tengah jalan setelah CREATE TABLE berhasil
        // tapi ALTER TABLE ADD INDEX gagal (nama index terlalu panjang), sehingga tabel
        // sempat terbentuk sebagian tanpa migration tercatat selesai. Drop dulu supaya aman di-rerun.
        Schema::dropIfExists('microsite_link_button_trafficts');

        Schema::create('microsite_link_button_trafficts', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->unsignedBigInteger('microsite_link_buttons_id');
            $table->timestamps();

            $table->foreign('microsite_link_buttons_id', 'mlbt_button_id_foreign')
                ->references('id')->on('microsite_link_buttons')->onDelete('cascade');
            $table->index(['microsite_link_buttons_id', 'date'], 'mlbt_button_id_date_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('microsite_link_button_trafficts');
    }
};
