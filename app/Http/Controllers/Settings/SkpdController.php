<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Modules\Admin\Models\SubSkpd;
use Modules\Admin\Models\Skpd;

class SkpdController extends Controller
{
    public function edit()
    {
        $user = Auth::user();
        $subSkpd = SubSkpd::where('kode_sub_skpd', $user->kode_sub_skpd)->first();
        $skpd = null;

        if ($subSkpd) {
            $skpd = Skpd::where('kode_skpd', $subSkpd->kode_skpd)->first();
        }

        return Inertia::render('settings/Skpd', [
            'skpd' => $skpd,
            'subSkpd' => $subSkpd,
        ]);
    }

    public function update(Request $request)
    {
        $user = Auth::user();
        $subSkpd = SubSkpd::where('kode_sub_skpd', $user->kode_sub_skpd)->first();
        
        if (!$subSkpd) {
            return back()->with('error', 'SKPD tidak ditemukan.');
        }

        $skpd = Skpd::where('kode_skpd', $subSkpd->kode_skpd)->first();
        
        if (!$skpd) {
            return back()->with('error', 'SKPD tidak ditemukan.');
        }

        $request->validate([
            'nama_skpd' => 'required|string|max:255',
            'npwp' => 'nullable|string|max:255',
        ]);

        $skpd->update([
            'nama_skpd' => $request->nama_skpd,
            'npwp' => $request->npwp,
        ]);

        return back()->with('success', 'Informasi SKPD berhasil diperbarui.');
    }
}
