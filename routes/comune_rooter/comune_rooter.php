<?php

use App\Http\Controllers\Api\ComuneController; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

//Routes Comune
Route::prefix('v1/comunes')->group(function () {
Route::post('/', [ComuneController::class, 'store'])->name('comune.store');
Route::get('/', [ComuneController::class, 'index'])->name('comune.index');
Route::put('/{id}', [ComuneController::class, 'update'])->name('comune.update');
Route::delete('/{id}', [ComuneController::class, 'destroy'])->name('comune.destroy');
Route::get('/show/deleted', [ComuneController::class, 'show_deleted'])->name('comune.show_deleted');
Route::get('/restore_one/{id}', [ComuneController::class, 'restore_one'])->name('comune.restore_one');
Route::get('/restore_all', [ComuneController::class, 'restore_all'])->name('comune.restore_all');
});