<?php

namespace Modules\Admin\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Admin\Models\Skpd;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\QueryException;

class SkpdController extends Controller
{
    public function index()
    {
        return Inertia::render('admin/skpd/Index');
    }

    public function getData(Request $request)
    {
        $query = Skpd::query();

        if ($request->has('search')) {
            $query->where('nama_skpd', 'like', "%{$request->search}%")
                  ->orWhere('kode_skpd', 'like', "%{$request->search}%");
        }

        $data = $query->paginate(10);

        return response()->json([
            'status' => true,
            'message' => 'Sukses Ambil Data',
            'data' => $data
        ]);
    }

    public function create()
    {
        return Inertia::render('admin/skpd/Form', [
            'isEdit' => false,
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_skpd' => 'required|integer|unique:tb_skpd,id_skpd',
            'kode_skpd' => 'required|string|unique:tb_skpd,kode_skpd',
            'nama_skpd' => 'required|string',
            'tahun' => 'required|string',
        ]);

        if ($request->header('X-Inertia')) {
            $validator->validate();
        } else if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            Skpd::create($request->all());
            
            return $request->header('X-Inertia')
                ? redirect()->route('admin.skpd.index')->with('success', 'SKPD created successfully.')
                : response()->json(['success' => true, 'message' => 'SKPD created successfully.'], 200);
        } catch (QueryException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function edit(Skpd $skpd)
    {
        return Inertia::render('admin/skpd/Form', [
            'skpd' => $skpd,
            'isEdit' => true,
        ]);
    }

    public function update(Request $request, Skpd $skpd)
    {
        $validator = Validator::make($request->all(), [
            'id_skpd' => 'required|integer|unique:tb_skpd,id_skpd,' . $skpd->id,
            'kode_skpd' => 'required|string|unique:tb_skpd,kode_skpd,' . $skpd->id,
            'nama_skpd' => 'required|string',
            'tahun' => 'required|string',
        ]);

        if ($request->header('X-Inertia')) {
            $validator->validate();
        }

        try {
            $skpd->update($request->all());
            
            return $request->header('X-Inertia')
                ? redirect()->route('admin.skpd.index')->with('success', 'SKPD updated successfully.')
                : response()->json(['success' => true, 'message' => 'SKPD updated successfully.'], 200);
        } catch (QueryException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function destroy(Skpd $skpd)
    {
        $skpd->delete();
        return redirect()->route('admin.skpd.index')->with('success', 'SKPD deleted successfully.');
    }

    public function importFromApi(Request $request, \App\Services\SipdService $sipdService)
    {
        $tahun = session('tahun', $request->get('tahun', date('Y')));
        $data = $sipdService->getSkpd($tahun);

        if (!$data || !isset($data['result'])) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data dari API atau data kosong.'
            ], 500);
        }

        $count = 0;
        foreach ($data['result'] as $item) {
            Skpd::updateOrCreate(
                ['kode_skpd' => $item['kode_skpd']],
                [
                    'id_skpd' => $item['id_skpd'],
                    'nama_skpd' => $item['nama_skpd'],
                    'tahun' => $tahun
                ]
            );
            $count++;
        }

        return response()->json([
            'success' => true,
            'message' => "Berhasil mengimpor $count data SKPD."
        ]);
    }
}
