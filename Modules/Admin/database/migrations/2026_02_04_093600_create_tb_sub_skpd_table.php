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
        Schema::create('tb_sub_skpd', function (Blueprint $blueprint) {
            $blueprint->id();
            $blueprint->string('kode_skpd');
            $blueprint->string('kode_sub_skpd')->unique();
            $blueprint->string('nama_sub_skpd');
            $blueprint->string('tahun');
            $blueprint->timestamps();

            $blueprint->foreign('kode_skpd')->references('kode_skpd')->on('tb_skpd')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_sub_skpd');
    }
};
