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
        Schema::table('tb_laporan_realisasi', function (Blueprint $table) {
            $table->integer('bulan')->nullable()->after('tanggal_sp2d');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tb_laporan_realisasi', function (Blueprint $table) {
            $table->dropColumn('bulan');
        });
    }
};
