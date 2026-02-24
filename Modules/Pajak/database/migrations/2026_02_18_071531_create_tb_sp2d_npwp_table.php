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
        Schema::create('tb_sp2d_npwp', function (Blueprint $table) {
            $table->id();
            $table->string('sp2d_number')->unique();
            $table->string('npwp_bud')->nullable();
            $table->string('npwp_skpd')->nullable();
            $table->string('npwp_penerima')->nullable();
            $table->string('nama_penerima')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_sp2d_npwp');
    }
};
