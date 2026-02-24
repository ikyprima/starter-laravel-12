<?php

namespace Modules\Admin\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Admin\Models\SubSkpd;
use Modules\Admin\Models\Skpd;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\QueryException;

class SubSkpdController extends Controller
{
    public function index()
    {
        return Inertia::render('admin/sub-skpd/Index');
    }

    public function getData(Request $request)
    {
        $query = SubSkpd::with('skpd');

        if ($request->has('search')) {
            $query->where('nama_sub_skpd', 'like', "%{$request->search}%")
                  ->orWhere('kode_sub_skpd', 'like', "%{$request->search}%");
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
        $skpds = Skpd::all();
        return Inertia::render('admin/sub-skpd/Form', [
            'isEdit' => false,
            'skpds' => $skpds
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'kode_skpd' => 'required|string|exists:tb_skpd,kode_skpd',
            'kode_sub_skpd' => 'required|string|unique:tb_sub_skpd,kode_sub_skpd',
            'nama_sub_skpd' => 'required|string',
            'tahun' => 'required|string',
        ]);

        if ($request->header('X-Inertia')) {
            $validator->validate();
        } else if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            SubSkpd::create($request->all());
            
            return $request->header('X-Inertia')
                ? redirect()->route('admin.sub-skpd.index')->with('success', 'Sub SKPD created successfully.')
                : response()->json(['success' => true, 'message' => 'Sub SKPD created successfully.'], 200);
        } catch (QueryException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function edit(SubSkpd $subSkpd)
    {
        $skpds = Skpd::all();
        return Inertia::render('admin/sub-skpd/Form', [
            'subSkpd' => $subSkpd,
            'isEdit' => true,
            'skpds' => $skpds
        ]);
    }

    public function update(Request $request, SubSkpd $subSkpd)
    {
        $validator = Validator::make($request->all(), [
            'kode_skpd' => 'required|string|exists:tb_skpd,kode_skpd',
            'kode_sub_skpd' => 'required|string|unique:tb_sub_skpd,kode_sub_skpd,' . $subSkpd->id,
            'nama_sub_skpd' => 'required|string',
            'tahun' => 'required|string',
        ]);

        if ($request->header('X-Inertia')) {
            $validator->validate();
        }

        try {
            $subSkpd->update($request->all());
            
            return $request->header('X-Inertia')
                ? redirect()->route('admin.sub-skpd.index')->with('success', 'Sub SKPD updated successfully.')
                : response()->json(['success' => true, 'message' => 'Sub SKPD updated successfully.'], 200);
        } catch (QueryException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function destroy(SubSkpd $subSkpd)
    {
        $subSkpd->delete();
        return redirect()->route('admin.sub-skpd.index')->with('success', 'Sub SKPD deleted successfully.');
    }

    public function importFromApi(Request $request, \App\Services\SipdService $sipdService)
    {
        $tahun = session('tahun', $request->get('tahun', date('Y')));
        $data = $sipdService->getSubSkpd($tahun);

        if (!$data || !isset($data['result'])) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data dari API atau data kosong.'
            ], 500);
        }

        $count = 0;
        foreach ($data['result'] as $item) {
            // Ensure SKPD Induk exists
            if (Skpd::where('kode_skpd', $item['kode_skpd'])->exists()) {
                SubSkpd::updateOrCreate(
                    ['kode_sub_skpd' => $item['kode_sub_skpd']],
                    [
                        'kode_skpd' => $item['kode_skpd'],
                        'nama_sub_skpd' => $item['nama_sub_skpd'],
                        'tahun' => $tahun
                    ]
                );
                $count++;
            }
        }

        return response()->json([
            'success' => true,
            'message' => "Berhasil mengimpor $count data Sub SKPD."
        ]);
    }
}
