<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

require __DIR__.'/comune_rooter/comune_rooter.php';
require __DIR__.'/province_rooter/province_rooter.php';
require __DIR__.'/municipality_rooter/municipality_rooter.php';
require __DIR__.'/bank_rooter/bank_rooter.php';