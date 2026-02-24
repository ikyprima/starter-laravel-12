<?php

namespace Modules\Pajak\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BkuPajak extends Model
{
    use HasFactory;

    protected $table = 'tb_bku_pajak';

    protected $fillable = [
        'tanggal',
        'nomor_dokumen',
        'uraian',
        'penerimaan',
        'pengeluaran',
        'saldo',
        'kondisi_tunai',
        'kode_akun',
        'nama_pajak_potongan',
        'id_billing',
        'ntpn',
        'kode_skpd',
        'bulan',
        'tahun',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'penerimaan' => 'integer',
        'pengeluaran' => 'integer',
        'saldo' => 'integer',
        'bulan' => 'integer',
    ];
}
