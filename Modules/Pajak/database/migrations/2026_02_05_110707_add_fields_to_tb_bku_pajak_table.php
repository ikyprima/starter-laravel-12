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
        Schema::table('tb_bku_pajak', function (Blueprint $table) {
            $table->string('kode_skpd', 50)->nullable()->after('ntpn');
            $table->integer('bulan')->nullable()->after('kode_skpd');
            $table->string('tahun', 4)->nullable()->after('bulan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tb_bku_pajak', function (Blueprint $table) {
            $table->dropColumn(['kode_skpd', 'bulan', 'tahun']);
        });
    }
};
