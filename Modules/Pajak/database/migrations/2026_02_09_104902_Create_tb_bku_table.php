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
        Schema::create('tb_bku', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal');
            $table->string('nomor_dokumen');
            $table->text('uraian');
            $table->decimal('penerimaan', 15, 2);
            $table->decimal('pengeluaran', 15, 2);
            $table->decimal('saldo', 15, 2);
            $table->string('kondisi_tunai');
            $table->string('kode_akun');
            $table->string('nama_pajak_potongan');
            $table->string('id_billing');
            $table->string('ntpn'); 
            $table->year('tahun');
            $table->tinyInteger('bulan');
            $table->string('kode_skpd');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists('tb_bku');
    }
};
