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
        Schema::create('tb_laporan_realisasi', function (Blueprint $table) {
            $table->id();
              $table->string('kode_skpd', 50);
            $table->string('kode_sub_skpd', 50)->nullable();
            $table->string('kode_sub_kegiatan', 50)->nullable();
            $table->string('kode_rekening', 50);

            $table->string('nomor_dokumen', 100);
            $table->string('jenis_dokumen', 50);
            $table->string('jenis_transaksi', 50);

            $table->date('tanggal_dokumen');

            $table->text('keterangan_dokumen')->nullable();

            $table->decimal('nilai_realisasi', 20, 2)->default(0);
            $table->decimal('nilai_setoran', 20, 2)->default(0);

            $table->string('nomor_sp2d', 100)->nullable();
            $table->date('tanggal_sp2d')->nullable();
            $table->decimal('nilai_sp2d', 20, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_laporan_realisasi');
    }
};
