<?php

namespace Modules\Pajak\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TransaksiPajakLs extends Model
{
    use HasFactory;

    protected $table = 'tb_transaksi_pajak_ls';

    protected $fillable = [
        'tahun',
        'bulan',
        'nomor_dokumen',
        'nomor_sp2d',
        'nilai_belanja',
        'uraian_belanja',
        'dpp',
        'master_akun_pajak_id',
        'jumlah_pajak',
        'npwp',
        'ntpn',
        'id_billing',
        'nama_rekanan',
        'kode_skpd'
    ];

    public function masterAkunPajak(): BelongsTo
    {
        return $this->belongsTo(MasterAkunPajak::class, 'master_akun_pajak_id');
    }
}
