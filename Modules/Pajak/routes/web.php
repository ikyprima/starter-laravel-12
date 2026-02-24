<?php

use Illuminate\Support\Facades\Route;
use Modules\Pajak\Http\Controllers\PajakController;
use Modules\Pajak\Http\Controllers\MasterAkunPajakController;
use Modules\Pajak\Http\Controllers\PajakLsController;
use Modules\Pajak\Http\Controllers\PajakGuController;
use Modules\Pajak\Http\Controllers\Sp2dNpwpController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('pajaks', PajakController::class)->names('pajak');
    Route::post('pajaks/upload-bku', [PajakController::class, 'uploadBku'])->name('pajak.upload-bku');
    Route::post('pajaks/upload-bku-pajak', [PajakController::class, 'uploadBkuPajak'])->name('pajak.upload-bku-pajak');
    Route::get('webservice/cek-koneksi', [PajakController::class, 'cekKoneksi'])->name('pajak.webservice.cek-koneksi');
    
    // Master Akun Pajak
    Route::group(['prefix' => 'master-akun-pajak', 'as' => 'pajak.master-akun.'], function () {
        Route::get('data', [MasterAkunPajakController::class, 'getData'])->name('data');
    });
    Route::resource('master-akun-pajak', MasterAkunPajakController::class)->names('pajak.master-akun');

    // Transaksi LS
    Route::group(['prefix' => 'pajak-ls', 'as' => 'pajak.ls.'], function () {
        Route::get('data', [PajakLsController::class, 'getData'])->name('data');
        Route::post('sync-bku', [PajakLsController::class, 'syncBku'])->name('sync-bku');
        Route::get('export', [PajakLsController::class, 'export'])->name('export');
    });
    Route::resource('pajak-ls', PajakLsController::class)->names('pajak.ls');

    // Transaksi GU
    Route::group(['prefix' => 'pajak-gu', 'as' => 'pajak.gu.'], function () {
        Route::get('data', [PajakGuController::class, 'getData'])->name('data');
        Route::post('sync-bku', [PajakGuController::class, 'syncBku'])->name('sync-bku');
        Route::post('destroy-by-period', [PajakGuController::class, 'destroyByPeriod'])->name('destroy-by-period');
        Route::get('export', [PajakGuController::class, 'export'])->name('export');
    });
    Route::resource('pajak-gu', PajakGuController::class)->names('pajak.gu');

    // SP2D NPWP
    Route::group(['prefix' => 'sp2d-npwp', 'as' => 'pajak.sp2d-npwp.'], function () {
        Route::get('data', [Sp2dNpwpController::class, 'getData'])->name('data');
        Route::post('import', [Sp2dNpwpController::class, 'import'])->name('import');
    });
    Route::resource('sp2d-npwp', Sp2dNpwpController::class)->names('pajak.sp2d-npwp');
});
