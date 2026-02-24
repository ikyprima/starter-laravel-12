<?php

use Illuminate\Support\Facades\Route;
use Modules\Pajak\Http\Controllers\PajakController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('pajaks', PajakController::class)->names('pajak');
});
