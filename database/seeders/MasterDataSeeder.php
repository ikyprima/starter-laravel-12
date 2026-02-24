<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MasterDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Seed tb_master_akun_pajak
        $masterPajak = [
            ["id"=>1,"kode_akun_pajak"=>"411121","jenis_pajak"=>"PPH 21"],
            ["id"=>2,"kode_akun_pajak"=>"411122","jenis_pajak"=>"Pajak Penghasilan Ps 22"],
            ["id"=>3,"kode_akun_pajak"=>"411123","jenis_pajak"=>"PPh Pasal 22 Impor"],
            ["id"=>4,"kode_akun_pajak"=>"411124","jenis_pajak"=>"Pajak Penghasilan Ps 23"],
            ["id"=>5,"kode_akun_pajak"=>"411125","jenis_pajak"=>"PPh Pasal 25/29 Orang Pribadi"],
            ["id"=>6,"kode_akun_pajak"=>"411126","jenis_pajak"=>"PPh Pasal 25/29 Badan"],
            ["id"=>7,"kode_akun_pajak"=>"411127","jenis_pajak"=>"PPh Pasal 26"],
            ["id"=>8,"kode_akun_pajak"=>"411128","jenis_pajak"=>"PPh Final"],
            ["id"=>9,"kode_akun_pajak"=>"411129","jenis_pajak"=>"PPh Non Migas Lainnya"],
            ["id"=>10,"kode_akun_pajak"=>"411131","jenis_pajak"=>"Fiskal Luar Negeri"],
            ["id"=>11,"kode_akun_pajak"=>"411111","jenis_pajak"=>"PPh Minyak Bumi"],
            ["id"=>12,"kode_akun_pajak"=>"411112","jenis_pajak"=>"PPh Gas Alam"],
            ["id"=>13,"kode_akun_pajak"=>"411119","jenis_pajak"=>"PPh Migas Lainnya"],
            ["id"=>14,"kode_akun_pajak"=>"411211","jenis_pajak"=>"Pajak Pertambahan Nilai"],
            ["id"=>15,"kode_akun_pajak"=>"411212","jenis_pajak"=>"PPN Impor"],
            ["id"=>16,"kode_akun_pajak"=>"411219","jenis_pajak"=>"PPN Lainnya"],
            ["id"=>17,"kode_akun_pajak"=>"411221","jenis_pajak"=>"PPnBM Dalam Negeri"],
            ["id"=>18,"kode_akun_pajak"=>"411222","jenis_pajak"=>"PPnBM Impor"],
            ["id"=>19,"kode_akun_pajak"=>"411229","jenis_pajak"=>"PPnBM Lainnya"],
            ["id"=>20,"kode_akun_pajak"=>"411611","jenis_pajak"=>"Bea Meterai"],
            ["id"=>21,"kode_akun_pajak"=>"411612","jenis_pajak"=>"Penjualan Benda Meterai"],
            ["id"=>22,"kode_akun_pajak"=>"411613","jenis_pajak"=>"Pajak Penjualan Batubara"],
            ["id"=>23,"kode_akun_pajak"=>"411619","jenis_pajak"=>"Pajak Tidak Langsung Lainnya"],
            ["id"=>24,"kode_akun_pajak"=>"411313","jenis_pajak"=>"Pajak Bumi dan Bangunan Sektor Perkebunan"],
            ["id"=>25,"kode_akun_pajak"=>"411314","jenis_pajak"=>"Pajak Bumi dan Bangunan Sektor Perhutanan"],
            ["id"=>26,"kode_akun_pajak"=>"411315","jenis_pajak"=>"Pajak Bumi dan Bangunan Sektor Pertambangan untuk Pertambangan Mineral dan Batubara"],
            ["id"=>27,"kode_akun_pajak"=>"411316","jenis_pajak"=>"Pajak Bumi dan Bangunan Sektor Pertambangan untuk Pertambangan Minyak Bumi dan Gas Bumi"],
            ["id"=>28,"kode_akun_pajak"=>"411317","jenis_pajak"=>"Pajak Bumi dan Bangunan Sektor Pertambangan untuk Pertambangan Panas Bumi"],
            ["id"=>29,"kode_akun_pajak"=>"411319","jenis_pajak"=>"Pajak Bumi dan Bangunan Sektor Lainnya"],
            ["id"=>30,"kode_akun_pajak"=>"411141","jenis_pajak"=>"PPh Pasal 21 Ditanggung Pemerintah"],
            ["id"=>31,"kode_akun_pajak"=>"411142","jenis_pajak"=>"PPh Pasal 22 Ditanggung Pemerintah"],
            ["id"=>32,"kode_akun_pajak"=>"411143","jenis_pajak"=>"PPh Pasal 22 Impor Ditanggung Pemerintah"],
            ["id"=>33,"kode_akun_pajak"=>"411144","jenis_pajak"=>"PPh Pasal 23 Ditanggung Pemerintah"],
            ["id"=>34,"kode_akun_pajak"=>"411145","jenis_pajak"=>"PPh Pasal 25/29 Orang Pribadi Ditanggung Pemerintah"],
            ["id"=>35,"kode_akun_pajak"=>"411146","jenis_pajak"=>"PPh Pasal 25/29 Badan Ditanggung Pemerintah"],
            ["id"=>36,"kode_akun_pajak"=>"411147","jenis_pajak"=>"PPh Pasal 26 Ditanggung Pemerintah"],
            ["id"=>37,"kode_akun_pajak"=>"411148","jenis_pajak"=>"PPh Final Ditanggung Pemerintah"],
            ["id"=>38,"kode_akun_pajak"=>"411149","jenis_pajak"=>"PPh Non Migas Lainnya Ditanggung Pemerintah"],
            ["id"=>39,"kode_akun_pajak"=>"411241","jenis_pajak"=>"PPN Ditanggung Pemerintah"],
            ["id"=>40,"kode_akun_pajak"=>"411242","jenis_pajak"=>"PPnBM Ditanggung Pemerintah"],
            ["id"=>41,"kode_akun_pajak"=>"411631","jenis_pajak"=>"Bunga/Denda Penagihan PPh Ditanggung Pemerintah"],
        ];

        foreach ($masterPajak as $data) {
            DB::table('tb_master_akun_pajak')->updateOrInsert(['id' => $data['id']], $data);
        }

        // 2. Seed tb_skpd
        $skpds = [
            ["id"=>1,"id_skpd"=>2559,"kode_skpd"=>"1.01.0.00.0.00.01.0000","nama_skpd"=>"DINAS PENDIDIKAN","npwp"=>null,"tahun"=>"2026"],
            ["id"=>2,"id_skpd"=>2560,"kode_skpd"=>"1.02.0.00.0.00.01.0000","nama_skpd"=>"DINAS KESEHATAN","npwp"=>null,"tahun"=>"2026"],
            ["id"=>3,"id_skpd"=>2561,"kode_skpd"=>"1.02.0.00.0.00.02.0000","nama_skpd"=>"RUMAH SAKIT UMUM DAERAH Dr. ACHMAD MOCHTAR BUKITTINGGI","npwp"=>null,"tahun"=>"2026"],
            ["id"=>4,"id_skpd"=>2562,"kode_skpd"=>"1.02.0.00.0.00.03.0000","nama_skpd"=>"RUMAH SAKIT JIWA Prof. HB. SAANIN","npwp"=>null,"tahun"=>"2026"],
            ["id"=>5,"id_skpd"=>2563,"kode_skpd"=>"1.02.0.00.0.00.04.0000","nama_skpd"=>"RUMAH SAKIT UMUM DAERAH MOHAMMAD NATSIR","npwp"=>null,"tahun"=>"2026"],
            ["id"=>6,"id_skpd"=>2564,"kode_skpd"=>"1.02.0.00.0.00.05.0000","nama_skpd"=>"RUMAH SAKIT UMUM DAERAH PROF. H. MUHAMMAD YAMIN, SH","npwp"=>null,"tahun"=>"2026"],
            ["id"=>7,"id_skpd"=>2565,"kode_skpd"=>"1.03.0.00.0.00.01.0000","nama_skpd"=>"DINAS BINA MARGA, CIPTA KARYA DAN TATA RUANG","npwp"=>null,"tahun"=>"2026"],
            ["id"=>8,"id_skpd"=>2588,"kode_skpd"=>"1.03.0.00.0.00.02.0000","nama_skpd"=>"DINAS SUMBER DAYA AIR DAN BINA KONSTRUKSI","npwp"=>null,"tahun"=>"2026"],
            ["id"=>9,"id_skpd"=>2591,"kode_skpd"=>"1.04.2.10.0.00.01.0000","nama_skpd"=>"DINAS PERUMAHAN RAKYAT, KAWASAN PERMUKIMAN DAN PERTANAHAN","npwp"=>null,"tahun"=>"2026"],
            ["id"=>10,"id_skpd"=>2592,"kode_skpd"=>"1.05.0.00.0.00.01.0000","nama_skpd"=>"SATUAN POLISI PAMONG PRAJA","npwp"=>null,"tahun"=>"2026"],
            ["id"=>11,"id_skpd"=>2593,"kode_skpd"=>"1.05.0.00.0.00.02.0000","nama_skpd"=>"BADAN PENANGGULANGAN BENCANA DAERAH","npwp"=>null,"tahun"=>"2026"],
            ["id"=>12,"id_skpd"=>2594,"kode_skpd"=>"1.06.0.00.0.00.01.0000","nama_skpd"=>"DINAS SOSIAL","npwp"=>null,"tahun"=>"2026"],
            ["id"=>13,"id_skpd"=>2603,"kode_skpd"=>"2.07.0.00.0.00.01.0000","nama_skpd"=>"DINAS TENAGA KERJA DAN TRANSMIGRASI","npwp"=>null,"tahun"=>"2026"],
            ["id"=>14,"id_skpd"=>2607,"kode_skpd"=>"2.08.2.14.0.00.01.0000","nama_skpd"=>"DINAS PEMBERDAYAAN PEREMPUAN DAN PERLINDUNGAN ANAK, PENGENDALIAN PENDUDUK DAN KELUARGA BERENCANA","npwp"=>null,"tahun"=>"2026"],
            ["id"=>15,"id_skpd"=>2609,"kode_skpd"=>"2.09.0.00.0.00.01.0000","nama_skpd"=>"DINAS PANGAN","npwp"=>null,"tahun"=>"2026"],
            ["id"=>16,"id_skpd"=>2612,"kode_skpd"=>"2.11.0.00.0.00.01.0000","nama_skpd"=>"DINAS LINGKUNGAN HIDUP","npwp"=>null,"tahun"=>"2026"],
            ["id"=>17,"id_skpd"=>2615,"kode_skpd"=>"2.12.0.00.0.00.01.0000","nama_skpd"=>"DINAS KEPENDUDUKAN DAN PENCATATAN SIPIL","npwp"=>null,"tahun"=>"2026"],
            ["id"=>18,"id_skpd"=>2616,"kode_skpd"=>"2.13.0.00.0.00.01.0000","nama_skpd"=>"DINAS PEMBERDAYAAN MASYARAKAT DAN DESA","npwp"=>null,"tahun"=>"2026"],
            ["id"=>19,"id_skpd"=>2617,"kode_skpd"=>"2.15.0.00.0.00.01.0000","nama_skpd"=>"DINAS PERHUBUNGAN","npwp"=>null,"tahun"=>"2026"],
            ["id"=>20,"id_skpd"=>2618,"kode_skpd"=>"2.16.2.20.2.21.01.0000","nama_skpd"=>"DINAS KOMUNIKASI, INFORMATIKA DAN STATISTIK","npwp"=>null,"tahun"=>"2026"],
            ["id"=>21,"id_skpd"=>2619,"kode_skpd"=>"2.17.0.00.0.00.01.0000","nama_skpd"=>"DINAS KOPERASI, USAHA KECIL DAN MENENGAH","npwp"=>null,"tahun"=>"2026"],
            ["id"=>22,"id_skpd"=>2621,"kode_skpd"=>"2.18.0.00.0.00.01.0000","nama_skpd"=>"DINAS PENANAMAN MODAL PROVINSI DAN PELAYANAN TERPADU SATU PINTU","npwp"=>null,"tahun"=>"2026"],
            ["id"=>23,"id_skpd"=>2622,"kode_skpd"=>"2.19.0.00.0.00.01.0000","nama_skpd"=>"DINAS PEMUDA DAN OLAHRAGA","npwp"=>null,"tahun"=>"2026"],
            ["id"=>24,"id_skpd"=>2624,"kode_skpd"=>"2.22.0.00.0.00.01.0000","nama_skpd"=>"DINAS KEBUDAYAAN","npwp"=>null,"tahun"=>"2026"],
            ["id"=>25,"id_skpd"=>2627,"kode_skpd"=>"2.23.2.24.0.00.01.0000","nama_skpd"=>"DINAS KEARSIPAN DAN PERPUSTAKAAN","npwp"=>null,"tahun"=>"2026"],
            ["id"=>26,"id_skpd"=>2628,"kode_skpd"=>"3.25.0.00.0.00.01.0000","nama_skpd"=>"DINAS KELAUTAN DAN PERIKANAN","npwp"=>null,"tahun"=>"2026"],
            ["id"=>27,"id_skpd"=>2635,"kode_skpd"=>"3.26.0.00.0.00.01.0000","nama_skpd"=>"DINAS PARIWISATA","npwp"=>null,"tahun"=>"2026"],
            ["id"=>28,"id_skpd"=>2636,"kode_skpd"=>"3.27.0.00.0.00.01.0000","nama_skpd"=>"DINAS PERKEBUNAN, TANAMAN PANGAN DAN HOLTIKULTURA","npwp"=>null,"tahun"=>"2026"],
            ["id"=>29,"id_skpd"=>2643,"kode_skpd"=>"3.27.0.00.0.00.02.0000","nama_skpd"=>"DINAS PETERNAKAN DAN KESEHATAN HEWAN","npwp"=>null,"tahun"=>"2026"],
            ["id"=>30,"id_skpd"=>2649,"kode_skpd"=>"3.28.0.00.0.00.01.0000","nama_skpd"=>"DINAS KEHUTANAN","npwp"=>null,"tahun"=>"2026"],
            ["id"=>31,"id_skpd"=>2661,"kode_skpd"=>"3.29.0.00.0.00.01.0000","nama_skpd"=>"DINAS ENERGI DAN SUMBER DAYA MINERAL","npwp"=>null,"tahun"=>"2026"],
            ["id"=>32,"id_skpd"=>2662,"kode_skpd"=>"3.30.3.31.0.00.01.0000","nama_skpd"=>"DINAS PERINDUSTRIAN DAN PERDAGANGAN","npwp"=>null,"tahun"=>"2026"],
            ["id"=>33,"id_skpd"=>2666,"kode_skpd"=>"4.01.0.00.0.00.01.0000","nama_skpd"=>"SEKRETARIAT DAERAH","npwp"=>null,"tahun"=>"2026"],
            ["id"=>34,"id_skpd"=>2676,"kode_skpd"=>"4.02.0.00.0.00.01.0000","nama_skpd"=>"SEKRETARIAT DPRD","npwp"=>null,"tahun"=>"2026"],
            ["id"=>35,"id_skpd"=>2677,"kode_skpd"=>"5.01.0.00.0.00.01.0000","nama_skpd"=>"BADAN PERENCANAAN PEMBANGUNAN DAERAH","npwp"=>null,"tahun"=>"2026"],
            ["id"=>36,"id_skpd"=>2678,"kode_skpd"=>"5.02.0.00.0.00.02.0000","nama_skpd"=>"BADAN PENDAPATAN DAERAH","npwp"=>null,"tahun"=>"2026"],
            ["id"=>37,"id_skpd"=>2679,"kode_skpd"=>"5.02.0.00.0.00.01.0000","nama_skpd"=>"BADAN PENGELOLAAN KEUANGAN DAN ASET DAERAH","npwp"=>"43.031.061.5.201.000","tahun"=>"2026"],
            ["id"=>38,"id_skpd"=>2680,"kode_skpd"=>"5.03.0.00.0.00.01.0000","nama_skpd"=>"BADAN KEPEGAWAIAN DAERAH","npwp"=>null,"tahun"=>"2026"],
            ["id"=>39,"id_skpd"=>2681,"kode_skpd"=>"5.04.0.00.0.00.01.0000","nama_skpd"=>"BADAN PENGEMBANGAN SUMBER DAYA MANUSIA","npwp"=>null,"tahun"=>"2026"],
            ["id"=>40,"id_skpd"=>2682,"kode_skpd"=>"5.05.0.00.0.00.01.0000","nama_skpd"=>"BADAN PENELITIAN DAN PENGEMBANGAN","npwp"=>null,"tahun"=>"2026"],
            ["id"=>41,"id_skpd"=>2683,"kode_skpd"=>"5.07.0.00.0.00.01.0000","nama_skpd"=>"BADAN PENGHUBUNG","npwp"=>null,"tahun"=>"2026"],
            ["id"=>42,"id_skpd"=>2684,"kode_skpd"=>"6.01.0.00.0.00.01.0000","nama_skpd"=>"INSPEKTORAT DAERAH PROVINSI","npwp"=>null,"tahun"=>"2026"],
            ["id"=>43,"id_skpd"=>2685,"kode_skpd"=>"8.01.0.00.0.00.01.0000","nama_skpd"=>"BADAN KESATUAN BANGSA DAN POLITIK","npwp"=>null,"tahun"=>"2026"],
        ];

        foreach ($skpds as $data) {
            DB::table('tb_skpd')->updateOrInsert(['id' => $data['id']], $data);
        }

        // 3. Seed tb_sub_skpd
        $subSkpds = [
            ["id"=>1,"kode_skpd"=>"1.01.0.00.0.00.01.0000","kode_sub_skpd"=>"1.01.0.00.0.00.01.0000","nama_sub_skpd"=>"DINAS PENDIDIKAN","tahun"=>"2026"],
            ["id"=>2,"kode_skpd"=>"1.01.0.00.0.00.01.0000","kode_sub_skpd"=>"1.01.0.00.0.00.01.0001","nama_sub_skpd"=>"UPTD BALAI TEKNOLOGI INFORMASI KOMUNIKASI PENDIDIKAN","tahun"=>"2026"],
            ["id"=>3,"kode_skpd"=>"1.01.0.00.0.00.01.0000","kode_sub_skpd"=>"1.01.0.00.0.00.01.0002","nama_sub_skpd"=>"CABANG DINAS PENDIDIKAN WILAYAH I (KOTA BUKITTINGGI, KOTA PADANG PANJANG DAN KAB. AGAM)","tahun"=>"2026"],
            ["id"=>4,"kode_skpd"=>"1.01.0.00.0.00.01.0000","kode_sub_skpd"=>"1.01.0.00.0.00.01.0003","nama_sub_skpd"=>"CABANG DINAS PENDIDIKAN WILAYAH II (KOTA PARIAMAN DAN KAB. PADANG PARIAMAN)","tahun"=>"2026"],
            ["id"=>5,"kode_skpd"=>"1.01.0.00.0.00.01.0000","kode_sub_skpd"=>"1.01.0.00.0.00.01.0004","nama_sub_skpd"=>"CABANG DINAS PENDIDIKAN WILAYAH III (KOTA SOLOK , KAB. SOLOK SELATAN DAN KAB. SOLOK)","tahun"=>"2026"],
            ["id"=>6,"kode_skpd"=>"1.01.0.00.0.00.01.0000","kode_sub_skpd"=>"1.01.0.00.0.00.01.0005","nama_sub_skpd"=>"CABANG DINAS PENDIDIKAN WILAYAH IV (KOTA PAYAKUMBUH, KAB. LIMA PULUH KOTA DAN KAB. TANAH DATAR)","tahun"=>"2026"],
            ["id"=>7,"kode_skpd"=>"1.01.0.00.0.00.01.0000","kode_sub_skpd"=>"1.01.0.00.0.00.01.0006","nama_sub_skpd"=>"CABANG DINAS PENDIDIKAN WILAYAH V (KOTA SAWAHLUNTO, KAB. SIJUNJUNG DAN KAB. DHARMASRAYA)","tahun"=>"2026"],
            ["id"=>8,"kode_skpd"=>"1.01.0.00.0.00.01.0000","kode_sub_skpd"=>"1.01.0.00.0.00.01.0007","nama_sub_skpd"=>"CABANG DINAS PENDIDIKAN WILAYAH VI (KAB. PASAMAN DAN KAB. PASAMAN BARAT)","tahun"=>"2026"],
            ["id"=>9,"kode_skpd"=>"1.01.0.00.0.00.01.0000","kode_sub_skpd"=>"1.01.0.00.0.00.01.0008","nama_sub_skpd"=>"CABANG DINAS PENDIDIKAN WILAYAH VII (KAB. PESISIR SELATAN)","tahun"=>"2026"],
            ["id"=>10,"kode_skpd"=>"1.01.0.00.0.00.01.0000","kode_sub_skpd"=>"1.01.0.00.0.00.01.0009","nama_sub_skpd"=>"CABANG DINAS PENDIDIKAN WILAYAH VIII (KAB. KEPULAUAN MENTAWAI)","tahun"=>"2026"],
            ["id"=>11,"kode_skpd"=>"1.02.0.00.0.00.01.0000","kode_sub_skpd"=>"1.02.0.00.0.00.01.0000","nama_sub_skpd"=>"DINAS KESEHATAN","tahun"=>"2026"],
            ["id"=>12,"kode_skpd"=>"1.02.0.00.0.00.01.0000","kode_sub_skpd"=>"1.02.0.00.0.00.01.0001","nama_sub_skpd"=>"UPTD Laboratorium Kesehatan Provinsi Sumatera Barat","tahun"=>"2026"],
            ["id"=>13,"kode_skpd"=>"1.02.0.00.0.00.01.0000","kode_sub_skpd"=>"1.02.0.00.0.00.01.0002","nama_sub_skpd"=>"UPTD BKOM & Pelkes","tahun"=>"2026"],
            ["id"=>14,"kode_skpd"=>"1.02.0.00.0.00.01.0000","kode_sub_skpd"=>"1.02.0.00.0.00.01.0003","nama_sub_skpd"=>"RUMAH SAKIT MATA SUMATERA BARAT","tahun"=>"2026"],
            ["id"=>15,"kode_skpd"=>"1.02.0.00.0.00.01.0000","kode_sub_skpd"=>"1.02.0.00.0.00.01.0004","nama_sub_skpd"=>"Rumah Sakit Paru Sumatera Barat","tahun"=>"2026"],
            ["id"=>16,"kode_skpd"=>"1.02.0.00.0.00.02.0000","kode_sub_skpd"=>"1.02.0.00.0.00.02.0000","nama_sub_skpd"=>"RUMAH SAKIT UMUM DAERAH Dr. ACHMAD MOCHTAR BUKITTINGGI","tahun"=>"2026"],
            ["id"=>17,"kode_skpd"=>"1.02.0.00.0.00.03.0000","kode_sub_skpd"=>"1.02.0.00.0.00.03.0000","nama_sub_skpd"=>"RUMAH SAKIT JIWA Prof. HB. SAANIN","tahun"=>"2026"],
            ["id"=>18,"kode_skpd"=>"1.02.0.00.0.00.04.0000","kode_sub_skpd"=>"1.02.0.00.0.00.04.0000","nama_sub_skpd"=>"RUMAH SAKIT UMUM DAERAH MOHAMMAD NATSIR","tahun"=>"2026"],
            ["id"=>19,"kode_skpd"=>"1.02.0.00.0.00.05.0000","kode_sub_skpd"=>"1.02.0.00.0.00.05.0000","nama_sub_skpd"=>"RUMAH SAKIT UMUM DAERAH PROF. H. MUHAMMAD YAMIN, SH","tahun"=>"2026"],
            ["id"=>20,"kode_skpd"=>"1.03.0.00.0.00.01.0000","kode_sub_skpd"=>"1.03.0.00.0.00.01.0000","nama_sub_skpd"=>"DINAS BINA MARGA, CIPTA KARYA DAN TATA RUANG","tahun"=>"2026"],
            ["id"=>33,"kode_skpd"=>"1.04.2.10.0.00.01.0000","kode_sub_skpd"=>"1.04.2.10.0.00.01.0000","nama_sub_skpd"=>"DINAS PERUMAHAN RAKYAT, KAWASAN PERMUKIMAN DAN PERTANAHAN","tahun"=>"2026"],
            ["id"=>34,"kode_skpd"=>"1.05.0.00.0.00.01.0000","kode_sub_skpd"=>"1.05.0.00.0.00.01.0000","nama_sub_skpd"=>"SATUAN POLISI PAMONG PRAJA","tahun"=>"2026"],
            ["id"=>35,"kode_skpd"=>"1.05.0.00.0.00.02.0000","kode_sub_skpd"=>"1.05.0.00.0.00.02.0000","nama_sub_skpd"=>"BADAN PENANGGULANGAN BENCANA DAERAH","tahun"=>"2026"],
            ["id"=>36,"kode_skpd"=>"1.06.0.00.0.00.01.0000","kode_sub_skpd"=>"1.06.0.00.0.00.01.0000","nama_sub_skpd"=>"DINAS SOSIAL","tahun"=>"2026"],
            ["id"=>114,"kode_skpd"=>"4.01.0.00.0.00.01.0000","kode_sub_skpd"=>"4.01.0.00.0.00.01.0000","nama_sub_skpd"=>"SEKRETARIAT DAERAH","tahun"=>"2026"],
            ["id"=>126,"kode_skpd"=>"5.02.0.00.0.00.01.0000","kode_sub_skpd"=>"5.02.0.00.0.00.01.0000","nama_sub_skpd"=>"BADAN PENGELOLAAN KEUANGAN DAN ASET DAERAH","tahun"=>"2026"],
            ["id"=>127,"kode_skpd"=>"5.02.0.00.0.00.02.0000","kode_sub_skpd"=>"5.02.0.00.0.00.02.0000","nama_sub_skpd"=>"BADAN PENDAPATAN DAERAH","tahun"=>"2026"],
        ];

        foreach ($subSkpds as $data) {
            DB::table('tb_sub_skpd')->updateOrInsert(['id' => $data['id']], $data);
        }
    }
}
