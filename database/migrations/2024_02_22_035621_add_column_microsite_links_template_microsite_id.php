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
        Schema::table('microsite_links', function (Blueprint $table) {
            $table->unsignedBigInteger('template_microsites_id')->nullable();
            $table->foreign('template_microsites_id')->references('id')->on('template_microsites')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('microsite_links', function (Blueprint $table) {
            //
        });
    }
};
