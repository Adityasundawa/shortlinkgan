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
        Schema::create('short_links_pop_unders', function (Blueprint $table) {
            $table->id();
            $table->text('url');
            $table->unsignedBigInteger('short_links_id');
            $table->foreign('short_links_id')->references('id')->on('short_links')->onDelete('cascade');
            $table->integer('precentage');
            $table->integer('count');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('short_links_pop_unders');
    }
};
