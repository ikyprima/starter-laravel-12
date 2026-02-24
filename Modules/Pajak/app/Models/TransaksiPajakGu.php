<?php

namespace Modules\Pajak\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TransaksiPajakGu extends Model
{
    use HasFactory;

    protected $table = 'tb_transaksi_pajak_gu';

    protected $fillable = [
        'tahun',
        'bulan',
        'nomor_tbp',
        'nilai_belanja',
        'uraian_belanja',
        'dpp',
        'master_akun_pajak_id',
        'jumlah_pajak',
        'npwp',
        'ntpn',
        'kode_skpd',
        'nama_rekanan',
        'id_billing'
    ];

    public function masterAkunPajak(): BelongsTo
    {
        return $this->belongsTo(MasterAkunPajak::class, 'master_akun_pajak_id');
    }
}
