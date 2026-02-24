<?php

use Illuminate\Support\Facades\Route;
use Modules\Admin\Http\Controllers\AdminController;
use Modules\Admin\Http\Controllers\DatabaseController;

Route::middleware(['auth', 'verified', 'role:admin'])->group(function () {
    Route::redirect('admin', '/admin/dashboards');
    Route::group(['prefix' => 'admin'], function () {
        Route::resource('dashboards', AdminController::class)->names('admin.dashboard');
        Route::resource('database-manager', DatabaseController::class)->names('admin.database-manager');
        Route::get('list-table', [DatabaseController::class, 'listTable'])->name('admin.list-table');
        Route::resource('users', \Modules\Admin\Http\Controllers\UserController::class)->names('admin.users');
        Route::get('users-data', [\Modules\Admin\Http\Controllers\UserController::class, 'getData'])->name('admin.users.data');
        Route::get('get-roles', [\Modules\Admin\Http\Controllers\UserController::class, 'getRoles'])->name('admin.get-roles');
        Route::get('get-sub-skpd', [\Modules\Admin\Http\Controllers\UserController::class, 'getSubSkpds'])->name('admin.get-sub-skpd');

        Route::resource('roles', \Modules\Admin\Http\Controllers\RoleController::class)->names('admin.roles');
        Route::get('roles-data', [\Modules\Admin\Http\Controllers\RoleController::class, 'getData'])->name('admin.roles.data');

        Route::resource('permissions', \Modules\Admin\Http\Controllers\PermissionController::class)->names('admin.permissions');
        Route::get('permissions-data', [\Modules\Admin\Http\Controllers\PermissionController::class, 'getData'])->name('admin.permissions.data');

        Route::resource('skpd', \Modules\Admin\Http\Controllers\SkpdController::class)->names('admin.skpd');
        Route::get('skpd-data', [\Modules\Admin\Http\Controllers\SkpdController::class, 'getData'])->name('admin.skpd.data');
        Route::get('skpd-import', [\Modules\Admin\Http\Controllers\SkpdController::class, 'importFromApi'])->name('admin.skpd.import');

        Route::resource('sub-skpd', \Modules\Admin\Http\Controllers\SubSkpdController::class)->names('admin.sub-skpd');
        Route::get('sub-skpd-data', [\Modules\Admin\Http\Controllers\SubSkpdController::class, 'getData'])->name('admin.sub-skpd.data');
        Route::get('sub-skpd-import', [\Modules\Admin\Http\Controllers\SubSkpdController::class, 'importFromApi'])->name('admin.sub-skpd.import');

        // BKU Pajak
        // Route::get('bku-pajak', [\Modules\Admin\Http\Controllers\BkuPajakController::class, 'index'])->name('admin.bku-pajak.index');
        // Route::get('bku-pajak-data', [\Modules\Admin\Http\Controllers\BkuPajakController::class, 'getData'])->name('admin.bku-pajak.data');
        // Route::post('bku-pajak-sync', [\Modules\Admin\Http\Controllers\BkuPajakController::class, 'syncApi'])->name('admin.bku-pajak.sync');
        // Route::post('bku-pajak-import', [\Modules\Admin\Http\Controllers\BkuPajakController::class, 'importExcel'])->name('admin.bku-pajak.import');

        // Laporan Realisasi
        // Route::get('laporan-realisasi', [\Modules\Admin\Http\Controllers\LaporanRealisasiController::class, 'index'])->name('admin.laporan-realisasi.index');
        // Route::get('laporan-realisasi-data', [\Modules\Admin\Http\Controllers\LaporanRealisasiController::class, 'getData'])->name('admin.laporan-realisasi.data');
        // Route::post('laporan-realisasi-sync', [\Modules\Admin\Http\Controllers\LaporanRealisasiController::class, 'syncApi'])->name('admin.laporan-realisasi.sync');
        // Route::post('laporan-realisasi-import', [\Modules\Admin\Http\Controllers\LaporanRealisasiController::class, 'importExcel'])->name('admin.laporan-realisasi.import');

        // Realisasi per SKPD
        Route::get('skpd/{skpd}/realisasi', [\Modules\Admin\Http\Controllers\LaporanRealisasiController::class, 'realisasiSkpd'])->name('admin.skpd.realisasi');
        Route::get('skpd/{skpd}/realisasi/{bulan}/detail', [\Modules\Admin\Http\Controllers\LaporanRealisasiController::class, 'realisasiDetail'])->name('admin.skpd.realisasi.detail');
        Route::post('skpd/realisasi-sync', [\Modules\Admin\Http\Controllers\LaporanRealisasiController::class, 'syncRealisasiSkpd'])->name('admin.skpd.realisasi.sync');

        // BKU Pajak per SKPD
        Route::get('skpd/{skpd}/bku-pajak', [\Modules\Admin\Http\Controllers\BkuPajakController::class, 'bkuSkpd'])->name('admin.skpd.bku-pajak');
        Route::get('skpd/{skpd}/bku-pajak/{bulan}/detail', [\Modules\Admin\Http\Controllers\BkuPajakController::class, 'bkuDetail'])->name('admin.skpd.bku-pajak.detail');
        Route::post('skpd/bku-pajak-sync', [\Modules\Admin\Http\Controllers\BkuPajakController::class, 'syncBkuSkpd'])->name('admin.skpd.bku-pajak.sync');

        // BKU per SKPD
        Route::get('skpd/{skpd}/bku', [\Modules\Admin\Http\Controllers\BkuController::class, 'bkuSkpd'])->name('admin.skpd.bku');
        Route::get('skpd/{skpd}/bku/{bulan}/detail', [\Modules\Admin\Http\Controllers\BkuController::class, 'bkuDetail'])->name('admin.skpd.bku.detail');
        Route::post('skpd/bku-sync', [\Modules\Admin\Http\Controllers\BkuController::class, 'syncBkuSkpd'])->name('admin.skpd.bku.sync');

        // Master Akun Pajak
        Route::resource('master-akun-pajak', \Modules\Pajak\Http\Controllers\MasterAkunPajakController::class)->names('admin.master-akun-pajak');
        Route::get('master-akun-pajak-data', [\Modules\Pajak\Http\Controllers\MasterAkunPajakController::class, 'getData'])->name('admin.master-akun-pajak.data');

        // SP2D NPWP
        Route::resource('sp2d-npwp', \Modules\Pajak\Http\Controllers\Sp2dNpwpController::class)->names('admin.sp2d-npwp');
        Route::get('sp2d-npwp-data', [\Modules\Pajak\Http\Controllers\Sp2dNpwpController::class, 'getData'])->name('admin.sp2d-npwp.data');
        Route::post('sp2d-npwp-import', [\Modules\Pajak\Http\Controllers\Sp2dNpwpController::class, 'import'])->name('admin.sp2d-npwp.import');
    });
    
});
