<?php

use App\Http\Controllers\Api\BankController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1/banks')->group(function () {
    Route::post('/', [BankController::class, 'store']);
    Route::put('/{id}', [BankController::class, 'update']);
    Route::delete('/{id}', [BankController::class, 'destroy']);
    Route::get('/', [BankController::class, 'index']);

    Route::get('/flat/trash/can', [BankController::class, 'flatTrashCan']);
    Route::get('/restore/all/', [BankController::class, 'restoreAllBanksFromRecycleBin']);

    Route::put('/recover/one/{id}', [BankController::class, 'recoverAnItemFromTheRecycleBin']);

    Route::delete('/permanently/delete/{id}', [BankController::class, 'permanentlyDeleteBank']);
});
