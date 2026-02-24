<?php

namespace Modules\Admin\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SubSkpd extends Model
{
    use HasFactory;

    protected $table = 'tb_sub_skpd';

    protected $fillable = [
        'kode_skpd',
        'kode_sub_skpd',
        'nama_sub_skpd',
        'tahun',
    ];

    public function skpd()
    {
        return $this->belongsTo(Skpd::class, 'kode_skpd', 'kode_skpd');
    }
}
