<?php
 
use App\Http\Controllers\Api\MunicipalityController; 
use Illuminate\Support\Facades\Route;

//Routes Municipality
Route::prefix('v1/municipalities')->group(function () {
Route::post('/', [MunicipalityController::class, 'store'])->name('municipality.store');
Route::get('/', [MunicipalityController::class, 'index'])->name('municipality.index');
Route::put('/{id}', [MunicipalityController::class, 'update'])->name('municipality.update');
Route::delete('/{id}', [MunicipalityController::class, 'destroy'])->name('municipality.destroy');
Route::get('/show/deleted', [MunicipalityController::class, 'show_deleted'])->name('municipality.show_deleted');
Route::get('/restore_one/{id}', [MunicipalityController::class, 'restore_one'])->name('municipality.restore_one');
Route::get('/restore_all', [MunicipalityController::class, 'restore_all'])->name('municipality.restore_all');
});