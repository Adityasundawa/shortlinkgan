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
        Schema::table('campaigns', function (Blueprint $table) {
            // Tambahkan kolom jika belum ada
            if (!Schema::hasColumn('campaigns', 'user_id')) {
                $table->foreignId('user_id')->constrained('users')->onDelete('cascade')->after('id');
            }
            if (!Schema::hasColumn('campaigns', 'description')) {
                $table->text('description')->nullable()->after('name');
            }
            if (!Schema::hasColumn('campaigns', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('description');
            }
            // Jika kolom 'name' sudah ada, ini akan dilewati.
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
       Schema::table('campaigns', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn(['user_id', 'description', 'is_active']);
        });
    }
};
