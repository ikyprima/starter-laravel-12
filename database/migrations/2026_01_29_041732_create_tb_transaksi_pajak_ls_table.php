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
        Schema::create('tb_transaksi_pajak_ls', function (Blueprint $table) {
            $table->id();
            $table->year('tahun');
            $table->tinyInteger('bulan');
            $table->string('nomor_sp2d');
            $table->decimal('nilai_belanja', 20, 2);
            $table->text('uraian_belanja');
            $table->decimal('dpp', 20, 2);
            $table->foreignId('master_akun_pajak_id')->constrained('tb_master_akun_pajak');
            $table->decimal('jumlah_pajak', 20, 2);
            $table->string('npwp');
            $table->string('ntpn', 16);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_transaksi_pajak_ls');
    }
};
