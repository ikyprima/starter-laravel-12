<?php

namespace Modules\Pajak\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Bku extends Model
{
    use HasFactory;

    protected $table = 'tb_bku';

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
        'tahun',
        'bulan',
        'kode_skpd',
    ];
}
