<?php

namespace Modules\Pajak\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Modules\Pajak\Models\Sp2dNpwp;
use App\Imports\Sp2dNpwpImport;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\HeadingRowImport;
use Illuminate\Support\Facades\Auth;

class Sp2dNpwpController extends Controller
{
    public function index()
    {
        if (!Auth::user()->hasRole('admin') && !Auth::user()->hasRole('Super Admin')) {
            abort(403);
        }

        $routeName = request()->route()->getName();
        if (str_contains($routeName, 'admin')) {
            return Inertia::render('admin/sp2d-npwp/Index');
        }

        return Inertia::render('pajak/sp2d-npwp/Index');
    }

    public function getData(Request $request)
    {
        if (!Auth::user()->hasRole('admin') && !Auth::user()->hasRole('Super Admin')) {
            abort(403);
        }

        $query = Sp2dNpwp::query();

        if ($request->search) {
            $query->where('sp2d_number', 'like', '%' . $request->search . '%')
                ->orWhere('nama_penerima', 'like', '%' . $request->search . '%');
        }

        $items = $query->latest()->paginate(10);

        $months = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];

        $items->getCollection()->transform(function ($item) use ($months) {
            $item->bulan_name = $months[$item->bulan] ?? '-';
            return $item;
        });

        return response()->json([
            'status' => true,
            'message' => 'Sukses Ambil Data',
            'data' => $items
        ]);
    }

    public function import(Request $request)
    {
        if (!Auth::user()->hasRole('admin') && !Auth::user()->hasRole('Super Admin')) {
            abort(403);
        }

        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv',
            'bulan' => 'required|integer|min:1|max:12'
        ]);

        $tahun = session('tahun') ?? date('Y');
        $bulan = $request->bulan;

        try {
            Excel::import(new Sp2dNpwpImport($bulan, $tahun), $request->file('file'));
            return redirect()->back()->with('success', 'Data SP2D NPWP berhasil diimport.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal memproses file: ' . $e->getMessage());
        }
    }

    public function destroy(Sp2dNpwp $sp2dNpwp)
    {
        if (!Auth::user()->hasRole('admin') && !Auth::user()->hasRole('Super Admin')) {
            abort(403);
        }

        $sp2dNpwp->delete();
        return redirect()->back()->with('success', 'Data berhasil dihapus.');
    }
}
