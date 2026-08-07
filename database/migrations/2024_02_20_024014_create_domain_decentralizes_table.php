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
        Schema::create('domain_decentralizes', function (Blueprint $table) {
            $table->id();
            $table->string('domain_url');
            $table->string('api_key');
            $table->enum('status', ['enable', 'disable'])->default('enable');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('domain_decentralizes');
    }
};
