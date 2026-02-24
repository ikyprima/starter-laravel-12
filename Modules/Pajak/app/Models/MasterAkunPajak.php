<?php

namespace Modules\Pajak\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MasterAkunPajak extends Model
{
    use HasFactory;

    protected $table = 'tb_master_akun_pajak';

    protected $fillable = [
        'kode_akun_pajak',
        'jenis_pajak',
    ];
}
