<?php

namespace Modules\Pajak\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Modules\Pajak\Models\MasterAkunPajak;
use Illuminate\Support\Facades\Validator;

class MasterAkunPajakController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $routeName = request()->route()->getName();
        if (str_contains($routeName, 'admin')) {
            return Inertia::render('admin/master-akun-pajak/Index');
        }
    }

    /**
     * Get data for pagination and search.
     */
    public function getData(Request $request)
    {
        $query = MasterAkunPajak::query();

        if ($request->search) {
            $query->where('kode_akun_pajak', 'like', '%' . $request->search . '%')
                ->orWhere('jenis_pajak', 'like', '%' . $request->search . '%');
        }

        $items = $query->latest()->paginate(10);

        return response()->json([
            'status' => true,
            'message' => 'Sukses Ambil Data',
            'data' => $items
        ], 200);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('pajak/master-akun-pajak/Form', [
            'isEdit' => false,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'kode_akun_pajak' => 'required|unique:tb_master_akun_pajak,kode_akun_pajak',
            'jenis_pajak' => 'required|string|max:255',
        ]);

        if ($request->expectsJson() || $request->ajax()) {
            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
        } else {
            if ($request->header('X-Inertia')) {
                $validator->validate();
            }
        }

        MasterAkunPajak::create($request->all());

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Master Akun Pajak berhasil dibuat.'], 200);
        }

        return redirect()->route('pajak.master-akun.index')->with('success', 'Master Akun Pajak berhasil dibuat.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $masterAkun = MasterAkunPajak::findOrFail($id);
        
        return Inertia::render('pajak/master-akun-pajak/Form', [
            'item' => $masterAkun,
            'isEdit' => true,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $masterAkun = MasterAkunPajak::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'kode_akun_pajak' => 'required|unique:tb_master_akun_pajak,kode_akun_pajak,' . $masterAkun->id,
            'jenis_pajak' => 'required|string|max:255',
        ]);

        if ($request->expectsJson() || $request->ajax()) {
            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
        } else {
            if ($request->header('X-Inertia')) {
                $validator->validate();
            }
        }

        $masterAkun->update($request->all());

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Master Akun Pajak berhasil diperbarui.'], 200);
        }

        return redirect()->route('pajak.master-akun.index')->with('success', 'Master Akun Pajak berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $masterAkun = MasterAkunPajak::findOrFail($id);
        $masterAkun->delete();

        if (request()->expectsJson() || request()->ajax()) {
             return response()->json(['success' => true, 'message' => 'Master Akun Pajak berhasil dihapus.'], 200);
        }

        return redirect()->back()->with('success', 'Master Akun Pajak berhasil dihapus.');
    }
}
