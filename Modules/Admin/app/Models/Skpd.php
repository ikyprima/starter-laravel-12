<?php

namespace Modules\Admin\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Skpd extends Model
{
    use HasFactory;

    protected $table = 'tb_skpd';

    protected $fillable = [
        'id_skpd',
        'kode_skpd',
        'nama_skpd',
        'npwp',
        'tahun',
    ];

    public function subSkpds()
    {
        return $this->hasMany(SubSkpd::class, 'kode_skpd', 'kode_skpd');
    }
}
