<?php

namespace App\Imports;

use Modules\Pajak\Models\Sp2dNpwp;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class Sp2dNpwpImport implements WithMultipleSheets
{
    protected $bulan;
    protected $tahun;

    public function __construct($bulan, $tahun)
    {
        $this->bulan = $bulan;
        $this->tahun = $tahun;
    }

    /**
     * Mengatur agar hanya sheet dengan nama 'DTH' yang diproses.
     */
    public function sheets(): array
    {
        return [
            'DTH' => new DthSheetImport($this->bulan, $this->tahun),
        ];
    }
}

/**
 * Class terpisah untuk menangani logic import pada sheet DTH.
 */
class DthSheetImport implements ToModel, WithStartRow
{
    protected $bulan;
    protected $tahun;

    public function __construct($bulan, $tahun)
    {
        $this->bulan = $bulan;
        $this->tahun = $tahun;
    }

    public function startRow(): int
    {
        return 2;
    }

    public function model(array $row)
    {
        /**
         * Akses index langsung (0-indexed).
         * Kolom B (Index 1) = Nomor SP2D
         * Kolom H (Index 7) = NPWP BUD
         * Kolom I (Index 8) = NPWP SKPD
         * Kolom J (Index 9) = NPWP Penerima
         * Kolom K (Index 10) = Nama Penerima
         */
        
        $sp2dNumber   = isset($row[1]) ? trim((string)$row[1]) : null;
        $npwpBud      = isset($row[7]) ? trim((string)$row[7]) : null;
        $npwpSkpd     = isset($row[8]) ? trim((string)$row[8]) : null;
        $npwpPenerima = isset($row[9]) ? trim((string)$row[9]) : null;
        $namaPenerima = isset($row[10]) ? trim((string)$row[10]) : null;

        // Validasi Nomor SP2D wajib ada
        if (!$sp2dNumber || strlen($sp2dNumber) < 5) {
            return null;
        }

        return Sp2dNpwp::updateOrCreate(
            ['sp2d_number' => $sp2dNumber],
            [
                'tahun'         => $this->tahun,
                'bulan'         => $this->bulan,
                'npwp_bud'      => $npwpBud,
                'npwp_skpd'     => $npwpSkpd,
                'npwp_penerima' => $npwpPenerima,
                'nama_penerima' => $namaPenerima,
            ]
        );
    }
}
