<?php

namespace Modules\Pajak\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Modules\Pajak\Models\TransaksiPajakLs;
use Modules\Pajak\Models\MasterAkunPajak;
use Modules\Pajak\Models\BkuPajak;
use Modules\Admin\Models\SubSkpd;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\Pajak\Models\Bku;
use Modules\Admin\Models\Skpd;
use App\Services\SipdService;
use Modules\Pajak\Models\Sp2dNpwp;
use Maatwebsite\Excel\Facades\Excel;

class PajakLsController extends Controller
{
    public function index()
    {
        $hasAccess = Auth::user()->hasRole('admin') || Auth::user()->hasRole('Super Admin');
        $skpds = [];

        if ($hasAccess) {
            $skpds = Skpd::orderBy('nama_skpd')->get();
        }

        return Inertia::render('pajak/transaksi-ls/Index', [
            'skpds' => $skpds,
            'isAdmin' => $hasAccess
        ]);
    }

    public function getData(Request $request)
    {
     
        $request->validate([
            'tahun' => 'required|integer',
            'bulan' => 'required|integer|between:1,12',
        ]);

        $query = TransaksiPajakLs::with('masterAkunPajak')
            ->where('tahun', $request->tahun)
            ->where('bulan', $request->bulan);

        if (Auth::user()->hasRole('admin') || Auth::user()->hasRole('Super Admin')) {
            if ($request->kode_skpd) {
                // Filter by SKPD parent code prefix instead of exact match
                $query->where('kode_skpd', 'like', $request->kode_skpd . '%');
            } else {
                $query->whereRaw('1 = 0'); // Return empty if no SKPD selected
            }
        } else {
            $query->where('kode_skpd', Auth::user()->kode_sub_skpd);
        }

        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('nomor_sp2d', 'like', '%' . $request->search . '%')
                  ->orWhere('uraian_belanja', 'like', '%' . $request->search . '%')
                  ->orWhere('ntpn', 'like', '%' . $request->search . '%');
            });
        }

        $items = $query->latest()->paginate(10);

        // Auto-fill NPWP and Nama Rekanan if null from SP2D NPWP master data
        $numbersToLookup = $items->getCollection()
            ->filter(fn($item) => empty($item->npwp) || empty($item->nama_rekanan))
            ->pluck('nomor_sp2d')
            ->unique()
            ->filter()
            ->values();

        if ($numbersToLookup->isNotEmpty()) {
            $sp2dMap = Sp2dNpwp::whereIn('sp2d_number', $numbersToLookup)
                ->where('tahun', $request->tahun)
                ->where('bulan', $request->bulan)
                ->get()
                ->keyBy('sp2d_number');

            $items->getCollection()->transform(function ($item) use ($sp2dMap) {
                if (empty($item->npwp) || empty($item->nama_rekanan)) {
                    $lookup = $sp2dMap->get($item->nomor_sp2d);
                    if ($lookup) {
                        $needsSave = false;
                        if (empty($item->npwp) && !empty($lookup->npwp_penerima)) {
                            $item->npwp = $lookup->npwp_penerima;
                            $needsSave = true;
                        }
                        if (empty($item->nama_rekanan) && !empty($lookup->nama_penerima)) {
                            $item->nama_rekanan = $lookup->nama_penerima;
                            $needsSave = true;
                        }
                        if ($needsSave) {
                            $item->save();
                        }
                    }
                }
                return $item;
            });
        }

        return response()->json([
            'status' => true,
            'message' => 'Sukses Ambil Data',
            'data' => $items
        ], 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tahun' => 'required|integer',
            'bulan' => 'required|integer|between:1,12',
            'nomor_sp2d' => 'nullable|string',
            'nomor_dokumen' => 'required|string',
            'nilai_belanja' => 'required|numeric|min:0',
            'uraian_belanja' => 'required|string',
            'dpp' => 'required|numeric|min:0',
            'master_akun_pajak_id' => 'required|exists:tb_master_akun_pajak,id',
            'jumlah_pajak' => 'required|numeric|min:0',
            'npwp' => 'required|string',
            'ntpn' => ['required', 'string', 'regex:/^[a-zA-Z0-9]{16}$/'],
        ]);

        if ($request->header('X-Inertia')) {
            $validator->validate();
        } else if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $request->all();
        $isAdmin = Auth::user()->hasRole('admin') || Auth::user()->hasRole('Super Admin');

        if ($isAdmin && $request->kode_skpd) {
            $data['kode_skpd'] = $request->kode_skpd;
        } else if (Auth::check()) {
            $data['kode_skpd'] = Auth::user()->kode_sub_skpd;
        }

        TransaksiPajakLs::create($data);

        return $request->header('X-Inertia')
            ? redirect()->back()->with('success', 'Transaksi LS berhasil disimpan.')
            : response()->json(['success' => true, 'message' => 'Transaksi LS berhasil disimpan.'], 200);
    }

    public function update(Request $request, TransaksiPajakLs $pajakL)
    {
        $validator = Validator::make($request->all(), [
            'nomor_sp2d' => 'nullable|string',
            'nomor_dokumen' => 'required|string',
            'nilai_belanja' => 'required|numeric|min:0',
            'uraian_belanja' => 'required|string',
            'dpp' => 'required|numeric|min:0',
            'master_akun_pajak_id' => 'required|exists:tb_master_akun_pajak,id',
            'jumlah_pajak' => 'required|numeric|min:0',
            'npwp' => 'required|string',
            'ntpn' => ['required', 'string', 'regex:/^[a-zA-Z0-9]{16}$/'],
        ]);

        if ($request->header('X-Inertia')) {
            $validator->validate();
        } else if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $pajakL->update($request->all());

        return $request->header('X-Inertia')
            ? redirect()->back()->with('success', 'Transaksi LS berhasil diperbarui.')
            : response()->json(['success' => true, 'message' => 'Transaksi LS berhasil diperbarui.'], 200);
    }

    public function destroy(TransaksiPajakLs $pajakL)
    {
        $pajakL->delete();
        return redirect()->back()->with('success', 'Transaksi LS berhasil dihapus.');
    }

    public function syncBku(Request $request, SipdService $sipdService)
    {
        $request->validate([
            'tahun' => 'required|integer',
            'bulan' => 'required|integer|between:1,12',
        ]);

        $isAdmin = Auth::user()->hasRole('admin') || Auth::user()->hasRole('Super Admin');
        $kode_skpd = '';

        if ($isAdmin && $request->kode_skpd) {
            $kode_skpd = $request->kode_skpd;
        } else {
            $user = Auth::user();
            $subSkpd = SubSkpd::where('kode_sub_skpd', $user->kode_sub_skpd)->first();
            
            if (!$subSkpd) {
                return response()->json(['status' => false, 'message' => 'SKPD User tidak ditemukan'], 404);
            }
            $kode_skpd = $subSkpd->kode_skpd;
        }
        $bulan = (int)$request->bulan;
        $tahun = (int)$request->tahun;

        // Dapatkan id_skpd dari tabel Skpd berdasarkan kode_skpd
        $skpd = Skpd::where('kode_skpd', $kode_skpd)->first();
        if (!$skpd) {
            return response()->json(['status' => false, 'message' => 'Data SKPD tidak ditemukan di database'], 404);
        }
        $idskpd = $skpd->id_skpd;

        // Cek apakah data BKU sudah ada, jika tidak ada atau force_bku = true, sync dari External API
        $forceBku = $request->boolean('force_bku');
        $bkuExists = Bku::where('kode_skpd', $kode_skpd)
            ->where('bulan', $bulan)
            ->where('tahun', $tahun)
            ->exists();

        if (!$bkuExists || $forceBku) {
            try {
                $bkuData = $sipdService->getBku($idskpd, $bulan, $tahun);
                
                if ($bkuData && isset($bkuData['status']) && $bkuData['status'] && isset($bkuData['data'])) {
                    DB::transaction(function () use ($bkuData, $kode_skpd, $bulan, $tahun, $forceBku) {
                        if ($forceBku) {
                            Bku::where('kode_skpd', $kode_skpd)
                                ->where('bulan', $bulan)
                                ->where('tahun', $tahun)
                                ->delete();
                        }

                        foreach ($bkuData['data'] as $item) {
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
                                'kode_skpd' => $item['kode_skpd'] ?? $kode_skpd,
                                'bulan' => $bulan,
                                'tahun' => $tahun,
                            ]);
                        }
                    });
                    Log::info("Auto-sync BKU: {$kode_skpd} bulan {$bulan}/{$tahun} - " . count($bkuData['data']) . " records");
                }
            } catch (\Exception $e) {
                Log::error('Auto-sync BKU Error: ' . $e->getMessage());
            }
        }

        // Cek apakah data BKU Pajak sudah ada, jika tidak ada atau force_bku_pajak = true, sync dari External API
        $forceBkuPajak = $request->boolean('force_bku_pajak');
        $bkuPajakExists = BkuPajak::where('kode_skpd', $kode_skpd)
            ->where('bulan', $bulan)
            ->where('tahun', $tahun)
            ->exists();

        if (!$bkuPajakExists || $forceBkuPajak) {
            try {
                $bkuPajakData = $sipdService->getBkuPajak($idskpd, $bulan, $tahun);
                
                if ($bkuPajakData && isset($bkuPajakData['status']) && $bkuPajakData['status'] && isset($bkuPajakData['data'])) {
                    DB::transaction(function () use ($bkuPajakData, $kode_skpd, $bulan, $tahun, $forceBkuPajak) {
                        if ($forceBkuPajak) {
                            BkuPajak::where('kode_skpd', $kode_skpd)
                                ->where('bulan', $bulan)
                                ->where('tahun', $tahun)
                                ->delete();
                        }

                        foreach ($bkuPajakData['data'] as $item) {
                            BkuPajak::create([
                                'tanggal' => \Carbon\Carbon::parse($item['tanggal'])->format('Y-m-d'),
                                'nomor_dokumen' => $item['nomor_dokumen'],
                                'uraian' => $item['uraian'],
                                'penerimaan' => $item['penerimaan'],
                                'pengeluaran' => $item['pengeluaran'],
                                'saldo' => $item['saldo'],
                                'kondisi_tunai' => $item['kondisi_tunai'],
                                'kode_akun' => $item['kode_akun'],
                                'nama_pajak_potongan' => $item['nama_pajak_potongan'],
                                'id_billing' => $item['id_billing'] ?? '',
                                'ntpn' => $item['ntpn'] ?? '',
                                'kode_skpd' => $item['kode_skpd'] ?? $kode_skpd,
                                'bulan' => $bulan,
                                'tahun' => $tahun,
                            ]);
                        }
                    });
                    Log::info("Auto-sync BKU Pajak: {$kode_skpd} bulan {$bulan}/{$tahun} - " . count($bkuPajakData['data']) . " records");
                }
            } catch (\Exception $e) {
                Log::error('Auto-sync BKU Pajak Error: ' . $e->getMessage());
            }
        }
      
        // Ambil bku skpd
        $bkuSkpd = Bku::where('kode_skpd', $kode_skpd)
            ->where('bulan', $bulan)
            ->where('tahun', $tahun)
            ->where('penerimaan', 0)
            ->where('pengeluaran','>', 0)
            ->get()->keyBy('nomor_dokumen');

        // SELECT * FROM tb_bku_pajak WHERE kode_skpd = '...' AND bulan = ... AND tahun = ... 
        // AND penerimaan=0 AND saldo=0 AND nomor_dokumen LIKE '%/LS'
        $bkuItems = BkuPajak::where('kode_skpd', $kode_skpd)
            ->where('bulan', $bulan)
            ->where('tahun', $tahun)
            ->where('penerimaan', 0)
            ->where('saldo', 0) 
            ->where('nomor_dokumen', 'like', '%/LS/%')
            ->get();

        // Get SP2D NPWP data for mapping
        $sp2dNpwpList = Sp2dNpwp::where('tahun', $tahun)
            ->where('bulan', $bulan)
            ->get()
            ->keyBy('sp2d_number');

        if ($bkuItems->isEmpty()) {
            return response()->json([
                'status' => false,
                'message' => "Tidak ada data BKU Pajak yang memenuhi kriteria untuk disinkronkan."
            ], 404);
        }

        $count = 0;
        foreach ($bkuItems as $item) {
            // Find master akun pajak by nama_pajak_potongan (mapped to jenis_pajak)
            $masterAkun = MasterAkunPajak::where('jenis_pajak', 'like', '%' . $item->nama_pajak_potongan . '%')->first();
            $kode_akun_pajak = $masterAkun->kode_akun_pajak;

            // Extract number from nomor_dokumen if possible for nomor_sp2d, or just use it as is
            // Typically nomor_dokumen in BKU for LS is like "00001/LS/..."
            $nomor_dokumen = $item->nomor_dokumen;
            
            $nomor_sp2d = explode(' (', $nomor_dokumen)[0];
            $nilai_belanja = $bkuSkpd[$nomor_dokumen]->pengeluaran;

            if($kode_akun_pajak == 411121){
                $dpp = $nilai_belanja;
            }elseif($kode_akun_pajak == 411122){
                $dpp = $nilai_belanja - (11/111 *  $nilai_belanja );
            }elseif($kode_akun_pajak == 411124){
                $dpp = 100/2 * $item->pengeluaran;
            }elseif($kode_akun_pajak == 411211){
                $dpp = 100/111 * $nilai_belanja;
            }else{
                $dpp = 0;
            }

            $transaksi = TransaksiPajakLs::firstOrNew([
                'tahun' => $tahun,
                'bulan' => $bulan,
                'nomor_dokumen' => $item->nomor_dokumen,
            ]);

            $transaksi->nomor_sp2d = $nomor_sp2d;
            $transaksi->nilai_belanja = $nilai_belanja;
            $transaksi->uraian_belanja = $item->uraian;
            $transaksi->dpp = $dpp;
            $transaksi->master_akun_pajak_id = $masterAkun?->id;
            $transaksi->jumlah_pajak = $item->pengeluaran;
            $transaksi->id_billing = $item->id_billing;
            $transaksi->kode_skpd = $item->kode_skpd;

            // Jangan update jika sudah terisi
            $sp2dData = $sp2dNpwpList->get($nomor_sp2d);

            if (empty($transaksi->npwp)) {
                $transaksi->npwp = $sp2dData ? $sp2dData->npwp_penerima : '';
            }
            if (empty($transaksi->ntpn)) {
                $transaksi->ntpn = $item->ntpn ?: '';
            }
            if (empty($transaksi->nama_rekanan)) {
                $transaksi->nama_rekanan = $sp2dData ? $sp2dData->nama_penerima : '';
            }

            $transaksi->save();
            $count++;
        }

        return response()->json([
            'status' => true,
            'message' => "Berhasil menyinkronkan $count data dari BKU Pajak."
        ]);
    }

    public function export(Request $request)
    {
        $request->validate([
            'tahun' => 'required|integer',
            'bulan' => 'required|integer|between:1,12',
        ]);

        $tahun = $request->tahun;
        $bulan = $request->bulan;
        $kode_skpd = '';

        if (Auth::user()->hasRole('admin') || Auth::user()->hasRole('Super Admin')) {
            $kode_skpd = $request->kode_skpd;
        } else {
            $kode_skpd = Auth::user()->kode_sub_skpd;
        }

        $query = TransaksiPajakLs::with('masterAkunPajak')
            ->where('tahun', $tahun)
            ->where('bulan', $bulan);

        if ($kode_skpd) {
            $query->where('kode_skpd', 'like', $kode_skpd . '%');
        }

        $data = $query->get();

        $nama_skpd = 'Semua SKPD';
        if ($kode_skpd) {
            $skpd = Skpd::where('kode_skpd', $kode_skpd)->first();
            $nama_skpd = $skpd ? $skpd->nama_skpd : $kode_skpd;
        }

        $months = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];

        $meta = [
            'nama_skpd' => $nama_skpd,
            'bulan_name' => $months[$bulan],
            'tahun' => $tahun
        ];

        return Excel::download(new \App\Exports\LsExport($data, $meta), 'Transaksi_LS_' . $months[$bulan] . '_' . $tahun . '.xlsx');
    }
}
