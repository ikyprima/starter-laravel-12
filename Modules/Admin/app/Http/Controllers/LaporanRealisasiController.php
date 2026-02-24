<?php

namespace Modules\Admin\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Modules\Pajak\Models\LaporanRealisasi;
use Modules\Admin\Models\Skpd;
use App\Services\SipdService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class LaporanRealisasiController extends Controller
{
    public function index()
    {
        return Inertia::render('admin/laporan-realisasi/Index');
    }

    public function realisasiSkpd(Skpd $skpd)
    {
        $syncStatus = [];
        for ($i = 1; $i <= 12; $i++) {
            $exists = LaporanRealisasi::where('kode_skpd', $skpd->kode_skpd)
                ->where('bulan', $i)
                ->exists();
            
            if ($exists) {
                $syncStatus[$i] = [
                    'success' => true,
                    'message' => 'Sudah disinkronkan',
                    'loading' => false
                ];
            }
        }

        return Inertia::render('admin/skpd/RealisasiMonths', [
            'skpd' => $skpd,
            'initialSyncStatus' => (object)$syncStatus
        ]);
    }

    public function syncRealisasiSkpd(Request $request, SipdService $sipdService)
    {
        $idskpd = $request->id_skpd;
        $bulan = $request->bulan;

        if (!$idskpd || !$bulan) {
            return response()->json(['status' => false, 'message' => 'Parameter tidak lengkap'], 400);
        }

        $skpd = Skpd::where('id_skpd', $idskpd)->first();
        if (!$skpd) {
             return response()->json(['status' => false, 'message' => 'SKPD tidak ditemukan'], 404);
        }

        try {
            DB::beginTransaction();

            // Hapus data bulan tersebut untuk SKPD tersebut
            LaporanRealisasi::where('kode_skpd', $skpd->kode_skpd)
                ->where('bulan', $bulan)
                ->delete();

            // Get data from external API
            $data = $sipdService->getRealisasi($idskpd, $bulan);

            if (!$data || !isset($data['status']) || !$data['status']) {
                 DB::rollBack();
                 return response()->json([
                    'status' => false, 
                    'message' => 'Gagal mengambil data dari external API: ' . ($data['message'] ?? 'Unknown Error')
                ], 500);
            }

            $count = 0;
            if (isset($data['data']) && is_array($data['data'])) {
                foreach ($data['data'] as $item) {
                    LaporanRealisasi::create([
                        'kode_skpd' => $item['kode_skpd'],
                        'kode_sub_skpd' => $item['kode_sub_skpd'],
                        'kode_sub_kegiatan' => $item['kode_sub_giat'],
                        'kode_rekening' => $item['kode_rekening'],
                        'nomor_dokumen' => $item['nomor_dokumen'],
                        'jenis_dokumen' => $item['jenis_dokumen'],
                        'jenis_transaksi' => $item['jenis_transaksi'],
                        'tanggal_dokumen' => \Carbon\Carbon::parse($item['tanggal_dokumen'])->format('Y-m-d'),
                        'keterangan_dokumen' => $item['keterangan_dokumen'],
                        'nilai_realisasi' => $item['nilai_realisasi'],
                        'nilai_setoran' => $item['nilai_setoran'],
                        'nomor_sp2d' => $item['nomor_sp2d'],
                        'tanggal_sp2d' => $item['tanggal_sp2d'] ? \Carbon\Carbon::parse($item['tanggal_sp2d'])->format('Y-m-d') : null,
                        'nilai_sp2d' => $item['nilai_sp2d'],
                        'bulan' => $bulan,
                    ]);
                    $count++;
                }
            }

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => "Berhasil sinkronisasi $count data realisasi bulan $bulan"
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Sync Realisasi Error: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage()
            ], 500);
        }
    }

    public function realisasiDetail(Skpd $skpd, $bulan)
    {
        $data = LaporanRealisasi::where('kode_skpd', $skpd->kode_skpd)
            ->where('bulan', $bulan)
            ->get();

        return Inertia::render('admin/skpd/RealisasiDetail', [
            'skpd' => $skpd,
            'bulan' => (int)$bulan,
            'tahun' => date('Y'), // Assuming current year, or you can pass it from request
            'data' => $data
        ]);
    }

    public function getData(Request $request)
    {
        $query = LaporanRealisasi::query();

        if ($request->search) {
            $query->where('nomor_dokumen', 'like', "%{$request->search}%")
                ->orWhere('keterangan_dokumen', 'like', "%{$request->search}%")
                ->orWhere('nomor_sp2d', 'like', "%{$request->search}%");
        }

        $data = $query->latest('tanggal_dokumen')->paginate(10);

        return response()->json([
            'status' => true,
            'message' => 'Sukses Ambil Data',
            'data' => $data
        ], 200);
    }

    public function syncApi()
    {
        // Placeholder for External API Sync logic
        return response()->json([
            'status' => true,
            'message' => 'Sinkronisasi API dalam pengembangan'
        ]);
    }

    public function importExcel(Request $request)
    {
        // Placeholder for Excel Import logic
        return response()->json([
            'status' => true,
            'message' => 'Import Excel dalam pengembangan'
        ]);
    }
}
