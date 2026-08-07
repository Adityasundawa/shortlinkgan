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
        Schema::create('microsite_link_trafficts', function (Blueprint $table) {
            $table->id();
            $table->date('date')->nullable();
            $table->unsignedInteger('unique_visitor_day')->default(0);
            $table->unsignedInteger('visitor_day')->default(0);
            $table->unsignedBigInteger('microsite_links_id');
            $table->unsignedBigInteger('domain_decentralizes_id');
            $table->timestamps();
            $table->foreign('microsite_links_id')->references('id')->on('microsite_links')->onDelete('cascade');
            $table->foreign('domain_decentralizes_id')->references('id')->on('domain_decentralizes')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('microsite_link_trafficts');
    }
};
