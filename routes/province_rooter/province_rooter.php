<?php
 
use App\Http\Controllers\Api\ProvinceController; 
use Illuminate\Support\Facades\Route;
 
Route::prefix('v1/provinces')->group(function () {
Route::post('/', [ProvinceController::class, 'store'])->name('province.store');
Route::get('/', [ProvinceController::class, 'index'])->name('province.index');
Route::put('/{id}', [ProvinceController::class, 'update'])->name('province.update');
Route::get('/restore_one/{id}', [ProvinceController::class, 'restore_one'])->name('province.restore_one');
Route::get('/restore_all', [ProvinceController::class, 'restore_all'])->name('province.restore_all');
Route::get('/show/deleted', [ProvinceController::class, 'show_deleted'])->name('province.show_deleted');
Route::delete('/{id}', [ProvinceController::class, 'destroy'])->name('province.destroy');
});
 


