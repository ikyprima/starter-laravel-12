<?php

namespace Modules\Pajak\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Modules\Pajak\Database\Factories\Sp2dNpwpFactory;

class Sp2dNpwp extends Model
{
    use HasFactory;

    protected $table = 'tb_sp2d_npwp';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'tahun',
        'bulan',
        'sp2d_number',
        'npwp_bud',
        'npwp_skpd',
        'npwp_penerima',
        'nama_penerima',
    ];

    // protected static function newFactory(): Sp2dNpwpFactory
    // {
    //     // return Sp2dNpwpFactory::new();
    // }
}
