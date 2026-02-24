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
        Schema::table('tb_sp2d_npwp', function (Blueprint $table) {
            $table->year('tahun')->after('id')->nullable();
            $table->tinyInteger('bulan')->after('tahun')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tb_sp2d_npwp', function (Blueprint $table) {
            $table->dropColumn(['tahun', 'bulan']);
        });
    }
};
