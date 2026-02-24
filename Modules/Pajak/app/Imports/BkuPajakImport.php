<?php

namespace Modules\Pajak\Imports;

use Modules\Pajak\Models\BkuPajak;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Carbon\Carbon;

class BkuPajakImport implements ToModel, WithStartRow
{
    protected $bulan;
    protected $tahun;
    protected $kode_skpd;

    public function __construct($bulan, $tahun, $kode_skpd)
    {
        $this->bulan = $bulan;
        $this->tahun = $tahun;
        $this->kode_skpd = $kode_skpd;
    }

    public function startRow(): int
    {
        return 2;
    }

    public function model(array $row)
    {
        // Skip if nomor_dokumen is empty (index 2)
        if (empty($row[2]) || $row[2] == '-') {
            return null;
        }

        // Format tanggal (index 1)
        $tanggal = null;
        try {
            if (is_numeric($row[1])) {
                $tanggal = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row[1])->format('Y-m-d');
            } else {
                $tanggal = Carbon::parse($row[1])->format('Y-m-d');
            }
        } catch (\Exception $e) {
            // Log or skip
        }

        $penerimaan = $this->parseNumeric($row[8] ?? 0);
        $pengeluaran = $this->parseNumeric($row[9] ?? 0);
        $saldo = $this->parseNumeric($row[10] ?? 0);

        return new BkuPajak([
            'tanggal'             => $tanggal,
            'nomor_dokumen'       => $row[2],
            'uraian'              => $row[3],
            'penerimaan'          => $penerimaan,
            'pengeluaran'         => $pengeluaran,
            'saldo'               => $saldo,
            'kondisi_tunai'       => '',
            'kode_akun'           => $row[4] ?? '',
            'nama_pajak_potongan' => $row[5] ?? '',
            'id_billing'          => $row[6] ?? '',
            'ntpn'                => $row[7] ?? '',
            'kode_skpd'           => $this->kode_skpd,
            'bulan'               => $this->bulan,
            'tahun'               => $this->tahun,
        ]);
    }

    private function parseNumeric($value)
    {
        if (is_numeric($value)) {
            return (int) $value;
        }

        if (empty($value) || $value == '-') {
            return 0;
        }

        // Remove thousand separator (.) and replace decimal separator (,) with (.)
        // Example: 415.203.041,00 -> 415203041.00
        $clean = str_replace('.', '', $value);
        $clean = str_replace(',', '.', $clean);

        return (int) floatval($clean);
    }
}
