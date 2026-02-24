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
        Schema::create('tb_skpd', function (Blueprint $blueprint) {
            $blueprint->id();
            $blueprint->integer('id_skpd')->unique();
            $blueprint->string('kode_skpd')->unique();
            $blueprint->string('nama_skpd');
            $blueprint->string('npwp')->nullable();
            $blueprint->string('tahun');
            $blueprint->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_skpd');
    }
};
