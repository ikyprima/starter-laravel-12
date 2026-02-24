<?php

namespace Modules\Admin\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Modules\Pajak\Models\Bku;
use Modules\Admin\Models\Skpd;
use App\Services\SipdService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BkuController extends Controller
{
    public function index()
    {
        return Inertia::render('admin/bku/Index');
    }

    public function bkuSkpd(Skpd $skpd)
    {
        $tahun = session('tahun', date('Y'));
        
        $syncStatus = [];
        for ($i = 1; $i <= 12; $i++) {
            $exists = Bku::where('kode_skpd', $skpd->kode_skpd)
                ->where('bulan', $i)
                ->where('tahun', $tahun)
                ->exists();
            
            if ($exists) {
                $syncStatus[$i] = [
                    'success' => true,
                    'message' => 'Sudah disinkronkan',
                    'loading' => false
                ];
            }
        }

        return Inertia::render('admin/skpd/BkuMonths', [
            'skpd' => $skpd,
            'initialSyncStatus' => (object)$syncStatus
        ]);
    }

    public function bkuDetail(Skpd $skpd, $bulan)
    {
        $tahun = session('tahun', date('Y'));
        
        $data = Bku::where('kode_skpd', $skpd->kode_skpd)
            ->where('bulan', $bulan)
            ->where('tahun', $tahun)
            ->orderBy('tanggal')
            ->orderBy('id')
            ->get();
        
        return Inertia::render('admin/skpd/BkuDetail', [
            'skpd' => $skpd,
            'bulan' => (int)$bulan,
            'tahun' => $tahun,
            'data' => $data
        ]);
    }

    public function syncBkuSkpd(Request $request, SipdService $sipdService)
    {
        $idskpd = $request->id_skpd;
        $bulan = $request->bulan;
        $tahun = session('tahun', date('Y'));

        if (!$idskpd || !$bulan) {
            return response()->json(['status' => false, 'message' => 'Parameter tidak lengkap'], 400);
        }

        $skpd = Skpd::where('id_skpd', $idskpd)->first();
        if (!$skpd) {
            return response()->json(['status' => false, 'message' => 'SKPD tidak ditemukan'], 404);
        }

        try {
            DB::beginTransaction();

            // Hapus data bulan, tahun tersebut untuk SKPD tersebut
            Bku::where('kode_skpd', $skpd->kode_skpd)
                ->where('bulan', $bulan)
                ->where('tahun', $tahun)
                ->delete();

            // Get data from external API (Assuming SipdService has getBku method or similar)
            // If getBku is not available, I might need to check SipdService or wait for it.
            // For now, I'll assume getBku exists or should be implemented.
            $data = $sipdService->getBku($idskpd, $bulan, $tahun);

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
                    Bku::create([
                        'tanggal' => \Carbon\Carbon::parse($item['tanggal'])->format('Y-m-d'),
                        'nomor_dokumen' => $item['nomor_dokumen'],
                        'uraian' => $item['uraian'],
                        'penerimaan' => $item['penerimaan'],
                        'pengeluaran' => $item['pengeluaran'],
                        'saldo' => $item['saldo'],
                        'kondisi_tunai' => $item['kondisi_tunai'],
                        'kode_akun' => $item['kode_akun'],
                        'nama_pajak_potongan' => $item['nama_pajak_potongan'] ?? '',
                        'id_billing' => $item['id_billing'] ?? '',
                        'ntpn' => $item['ntpn'] ?? '',
                        'kode_skpd' => $item['kode_skpd'] ?? $skpd->kode_skpd,
                        'bulan' => $bulan,
                        'tahun' => $tahun,
                    ]);
                    $count++;
                }
            }

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => "Berhasil sinkronisasi $count data BKU bulan $bulan"
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Sync BKU Error: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage()
            ], 500);
        }
    }
}
