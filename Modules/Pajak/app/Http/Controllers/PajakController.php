<?php

namespace Modules\Pajak\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Pajak\Imports\BkuImport;
use Modules\Pajak\Imports\BkuPajakImport;
use Modules\Pajak\Models\Bku;
use Modules\Pajak\Models\BkuPajak;
use App\Services\SipdService;
use Illuminate\Support\Facades\Auth;


class PajakController extends Controller
{
    protected $sipdService;

    public function __construct(SipdService $sipdService)
    {
        $this->sipdService = $sipdService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('pajak::index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('pajak::create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request) {}

    /**
     * Show the specified resource.
     */
    public function show($id)
    {
        return view('pajak::show');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        return view('pajak::edit');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id) {}

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id) {}

    public function uploadBku(Request $request)
    {
        $user = Auth::user();
        $isAdmin = $user->hasRole('admin') || $user->hasRole('Super Admin');

        $rules = [
            'file' => 'required|mimes:xlsx,xls,csv',
            'bulan' => 'required|integer',
            'tahun' => 'required|integer',
        ];

        if ($isAdmin) {
            $rules['kode_skpd'] = 'required|string';
        }

        $request->validate($rules);

        $kode_skpd = $isAdmin ? $request->kode_skpd : $user->kode_sub_skpd;

        // Delete existing data for the same period and skpd
        Bku::where('bulan', $request->bulan)
            ->where('tahun', $request->tahun)
            ->where('kode_skpd', $kode_skpd)
            ->delete();

        try {
            Excel::import(new BkuImport($request->bulan, $request->tahun, $kode_skpd), $request->file('file'));
            return response()->json(['message' => 'Berhasil upload BKU']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Gagal upload BKU: ' . $e->getMessage()], 500);
        }
    }

    public function uploadBkuPajak(Request $request)
    {
        $user = Auth::user();
        $isAdmin = $user->hasRole('admin') || $user->hasRole('Super Admin');

        $rules = [
            'file' => 'required|mimes:xlsx,xls,csv',
            'bulan' => 'required|integer',
            'tahun' => 'required|integer',
        ];

        if ($isAdmin) {
            $rules['kode_skpd'] = 'required|string';
        }

        $request->validate($rules);

        $kode_skpd = $isAdmin ? $request->kode_skpd : $user->kode_sub_skpd;

        // Delete existing data for the same period and skpd
        BkuPajak::where('bulan', $request->bulan)
            ->where('tahun', $request->tahun)
            ->where('kode_skpd', $kode_skpd)
            ->delete();

        try {
            Excel::import(new BkuPajakImport($request->bulan, $request->tahun, $kode_skpd), $request->file('file'));
            return response()->json(['message' => 'Berhasil upload BKU Pajak']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Gagal upload BKU Pajak: ' . $e->getMessage()], 500);
        }
    }

    public function cekKoneksi()
    {
        $result = $this->sipdService->checkConnection();
        if ($result['success']) {
            return response()->json(['status' => 200, 'message' => $result['message']]);
        }
        return response()->json(['status' => 500, 'message' => $result['message']]);
    }
}
