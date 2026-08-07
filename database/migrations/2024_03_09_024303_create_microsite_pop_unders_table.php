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
        Schema::create('microsite_pop_unders', function (Blueprint $table) {
            $table->id();
            $table->text('url');
            $table->unsignedBigInteger('microsite_link_id');
            $table->foreign('microsite_link_id')->references('id')->on('microsite_links')->onDelete('cascade');
            $table->unsignedBigInteger('percentage');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('microsite_pop_unders');
    }
};
