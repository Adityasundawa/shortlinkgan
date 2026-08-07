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
        Schema::create('label_shortlinks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('labels_id');
            $table->unsignedBigInteger('users_id');
            $table->unsignedBigInteger('short_links_id');
            $table->timestamps();

            $table->foreign('short_links_id')->references('id')->on('short_links')->onDelete('cascade');
            $table->foreign('users_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('labels_id')->references('id')->on('labels')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('label_shortlinks');
    }
};
