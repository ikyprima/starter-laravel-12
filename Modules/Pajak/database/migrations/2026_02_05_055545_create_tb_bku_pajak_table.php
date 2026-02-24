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
        Schema::create('tb_bku_pajak', function (Blueprint $table) {
            $table->id();
            
            $table->date('tanggal');
            $table->string('nomor_dokumen');
            $table->text('uraian');

            $table->bigInteger('penerimaan')->default(0);
            $table->bigInteger('pengeluaran')->default(0);
            $table->bigInteger('saldo')->default(0);

            $table->string('kondisi_tunai')->nullable();

            $table->string('kode_akun', 50);
            $table->string('nama_pajak_potongan', 100);

            $table->string('id_billing', 50)->nullable();
            $table->string('ntpn', 50)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_bku_pajak');
    }
};
