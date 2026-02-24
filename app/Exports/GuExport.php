<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class GuExport implements FromArray, WithTitle, WithHeadings, WithStyles, WithCustomStartCell, WithEvents
{
    protected $data;
    protected $meta;

    public function __construct($data, $meta)
    {
        $this->data = $data;
        $this->meta = $meta;
    }

    public function array(): array
    {
        $rows = [];
        $totalPajak = 0;

        foreach ($this->data as $index => $item) {
            $totalPajak += $item->jumlah_pajak;
            $rows[] = [
                $index + 1,
                $item->nomor_tbp,
                $item->uraian_belanja,
                $item->nilai_belanja,
                $item->dpp,
                $item->masterAkunPajak ? $item->masterAkunPajak->kode_akun_pajak : '-',
                $item->masterAkunPajak ? $item->masterAkunPajak->jenis_pajak : '-',
                $item->jumlah_pajak,
                $item->npwp,
                $item->nama_rekanan,
                $item->ntpn,
            ];
        }

        // Add Total Row
        $rows[] = [
            'TOTAL',
            '',
            '',
            '',
            '',
            '',
            '',
            $totalPajak,
            '',
            '',
            ''
        ];

        return $rows;
    }

    public function headings(): array
    {
        return [
            'No',
            'Nomor TBP',
            'Uraian Belanja',
            'Nilai Belanja',
            'DPP',
            'Kode Akun Pajak',
            'Jenis Pajak',
            'Jumlah Pajak',
            'NPWP',
            'Nama Rekanan',
            'NTPN',
        ];
    }

    public function title(): string
    {
        return 'Transaksi GU ' . $this->meta['bulan_name'];
    }

    public function startCell(): string
    {
        return 'A7';
    }

    public function styles(Worksheet $sheet)
    {
        return [
            7 => [
                'font' => ['bold' => true],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'F2F2F2'],
                ],
            ],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $lastRow = $sheet->getHighestRow();

                // 1. Setup Header Info
                $sheet->setCellValue('A1', 'DAFTAR TRANSAKSI PAJAK GU');
                $sheet->mergeCells('A1:K1');
                $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
                $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                $sheet->setCellValue('A3', 'SKPD');
                $sheet->setCellValue('B3', ': ' . $this->meta['nama_skpd']);
                $sheet->mergeCells('B3:K3');

                $sheet->setCellValue('A4', 'Bulan');
                $sheet->setCellValue('B4', ': ' . $this->meta['bulan_name']);
                
                $sheet->setCellValue('A5', 'Tahun');
                $sheet->setCellValue('B5', ': ' . $this->meta['tahun']);

                // 2. Formatting Table
                $range = 'A7:K' . $lastRow;
                $sheet->getStyle($range)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
                
                // Centering some columns
                $sheet->getStyle('A8:A' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('F8:F' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                // Wrap text for Uraian Belanja (Column C)
                $sheet->getStyle('C8:C' . $lastRow)->getAlignment()->setWrapText(true);
                $sheet->getStyle('C8:C' . $lastRow)->getAlignment()->setVertical(Alignment::VERTICAL_TOP);

                // Formatting numbers
                $sheet->getStyle('D8:E' . $lastRow)->getNumberFormat()->setFormatCode('#,##0');
                $sheet->getStyle('H8:H' . $lastRow)->getNumberFormat()->setFormatCode('#,##0');

                // Align right for numeric columns
                $sheet->getStyle('D8:E' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                $sheet->getStyle('H8:H' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

                // 3. Style Total Row
                $sheet->mergeCells('A' . $lastRow . ':G' . $lastRow);
                $sheet->getStyle('A' . $lastRow . ':K' . $lastRow)->getFont()->setBold(true);
                $sheet->getStyle('A' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

                // Auto size columns
                foreach (range('A', 'K') as $columnID) {
                    if ($columnID === 'C') {
                        $sheet->getColumnDimension($columnID)->setWidth(50);
                    } else {
                        $sheet->getColumnDimension($columnID)->setAutoSize(true);
                    }
                }
            },
        ];
    }
}
