<?php

namespace Modules\Pajak\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Pajak\Models\MasterAkunPajak;

class MasterAkunPajakSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            ['kode_akun_pajak' => '411121', 'jenis_pajak' => 'PPh Pasal 21'],
            ['kode_akun_pajak' => '411122', 'jenis_pajak' => 'PPh Pasal 22'],
            ['kode_akun_pajak' => '411123', 'jenis_pajak' => 'PPh Pasal 22 Impor'],
            ['kode_akun_pajak' => '411124', 'jenis_pajak' => 'PPh Pasal 23'],
            ['kode_akun_pajak' => '411125', 'jenis_pajak' => 'PPh Pasal 25/29 Orang Pribadi'],
            ['kode_akun_pajak' => '411126', 'jenis_pajak' => 'PPh Pasal 25/29 Badan'],
            ['kode_akun_pajak' => '411127', 'jenis_pajak' => 'PPh Pasal 26'],
            ['kode_akun_pajak' => '411128', 'jenis_pajak' => 'PPh Final'],
            ['kode_akun_pajak' => '411129', 'jenis_pajak' => 'PPh Non Migas Lainnya'],
            ['kode_akun_pajak' => '411131', 'jenis_pajak' => 'Fiskal Luar Negeri'],
            ['kode_akun_pajak' => '411111', 'jenis_pajak' => 'PPh Minyak Bumi'],
            ['kode_akun_pajak' => '411112', 'jenis_pajak' => 'PPh Gas Alam'],
            ['kode_akun_pajak' => '411119', 'jenis_pajak' => 'PPh Migas Lainnya'],
            ['kode_akun_pajak' => '411211', 'jenis_pajak' => 'PPN Dalam Negeri'],
            ['kode_akun_pajak' => '411212', 'jenis_pajak' => 'PPN Impor'],
            ['kode_akun_pajak' => '411219', 'jenis_pajak' => 'PPN Lainnya'],
            ['kode_akun_pajak' => '411221', 'jenis_pajak' => 'PPnBM Dalam Negeri'],
            ['kode_akun_pajak' => '411222', 'jenis_pajak' => 'PPnBM Impor'],
            ['kode_akun_pajak' => '411229', 'jenis_pajak' => 'PPnBM Lainnya'],
            ['kode_akun_pajak' => '411611', 'jenis_pajak' => 'Bea Meterai'],
            ['kode_akun_pajak' => '411612', 'jenis_pajak' => 'Penjualan Benda Meterai'],
            ['kode_akun_pajak' => '411613', 'jenis_pajak' => 'Pajak Penjualan Batubara'],
            ['kode_akun_pajak' => '411619', 'jenis_pajak' => 'Pajak Tidak Langsung Lainnya'],
            ['kode_akun_pajak' => '411313', 'jenis_pajak' => 'Pajak Bumi dan Bangunan Sektor Perkebunan'],
            ['kode_akun_pajak' => '411314', 'jenis_pajak' => 'Pajak Bumi dan Bangunan Sektor Perhutanan'],
            ['kode_akun_pajak' => '411315', 'jenis_pajak' => 'Pajak Bumi dan Bangunan Sektor Pertambangan untuk Pertambangan Mineral dan Batubara'],
            ['kode_akun_pajak' => '411316', 'jenis_pajak' => 'Pajak Bumi dan Bangunan Sektor Pertambangan untuk Pertambangan Minyak Bumi dan Gas Bumi'],
            ['kode_akun_pajak' => '411317', 'jenis_pajak' => 'Pajak Bumi dan Bangunan Sektor Pertambangan untuk Pertambangan Panas Bumi'],
            ['kode_akun_pajak' => '411319', 'jenis_pajak' => 'Pajak Bumi dan Bangunan Sektor Lainnya'],
            ['kode_akun_pajak' => '411141', 'jenis_pajak' => 'PPh Pasal 21 Ditanggung Pemerintah'],
            ['kode_akun_pajak' => '411142', 'jenis_pajak' => 'PPh Pasal 22 Ditanggung Pemerintah'],
            ['kode_akun_pajak' => '411143', 'jenis_pajak' => 'PPh Pasal 22 Impor Ditanggung Pemerintah'],
            ['kode_akun_pajak' => '411144', 'jenis_pajak' => 'PPh Pasal 23 Ditanggung Pemerintah'],
            ['kode_akun_pajak' => '411145', 'jenis_pajak' => 'PPh Pasal 25/29 Orang Pribadi Ditanggung Pemerintah'],
            ['kode_akun_pajak' => '411146', 'jenis_pajak' => 'PPh Pasal 25/29 Badan Ditanggung Pemerintah'],
            ['kode_akun_pajak' => '411147', 'jenis_pajak' => 'PPh Pasal 26 Ditanggung Pemerintah'],
            ['kode_akun_pajak' => '411148', 'jenis_pajak' => 'PPh Final Ditanggung Pemerintah'],
            ['kode_akun_pajak' => '411149', 'jenis_pajak' => 'PPh Non Migas Lainnya Ditanggung Pemerintah'],
            ['kode_akun_pajak' => '411241', 'jenis_pajak' => 'PPN Ditanggung Pemerintah'],
            ['kode_akun_pajak' => '411242', 'jenis_pajak' => 'PPnBM Ditanggung Pemerintah'],
            ['kode_akun_pajak' => '411631', 'jenis_pajak' => 'Bunga/Denda Penagihan PPh Ditanggung Pemerintah'],
        ];

        foreach ($data as $item) {
            MasterAkunPajak::updateOrCreate(
                ['kode_akun_pajak' => $item['kode_akun_pajak']],
                ['jenis_pajak' => $item['jenis_pajak']]
            );
        }
    }
}
