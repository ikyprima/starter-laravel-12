<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::table('tb_transaksi_pajak_ls', function (Blueprint $table) {
            $table->string('kode_skpd')->nullable();
            $table->string('nama_rekanan')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::table('tb_transaksi_pajak_ls', function (Blueprint $table) {
            $table->dropColumn('kode_skpd');
            $table->dropColumn('nama_rekanan');
        });
    }
};
