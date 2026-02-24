<?php

namespace Modules\Pajak\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LaporanRealisasi extends Model
{
    use HasFactory;

    protected $table = 'tb_laporan_realisasi';

    protected $fillable = [
        'kode_skpd',
        'kode_sub_skpd',
        'kode_sub_kegiatan',
        'kode_rekening',
        'nomor_dokumen',
        'jenis_dokumen',
        'jenis_transaksi',
        'tanggal_dokumen',
        'keterangan_dokumen',
        'nilai_realisasi',
        'nilai_setoran',
        'nomor_sp2d',
        'tanggal_sp2d',
        'nilai_sp2d',
        'bulan',
    ];

    protected $casts = [
        'tanggal_dokumen' => 'date',
        'tanggal_sp2d' => 'date',
        'nilai_realisasi' => 'decimal:2',
        'nilai_setoran' => 'decimal:2',
        'nilai_sp2d' => 'decimal:2',
        'bulan' => 'integer',
    ];
}
