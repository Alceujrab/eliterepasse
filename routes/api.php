<?php

use App\Http\Controllers\Api\FipeController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// ─── FIPE API (pública com throttle) ─────────────────────────────────
Route::prefix('fipe')->middleware(['throttle:60,1'])->group(function () {
    Route::get('/marcas',              [FipeController::class, 'marcas']);
    Route::get('/modelos',             [FipeController::class, 'modelos']);
    Route::get('/anos',                [FipeController::class, 'anos']);
    Route::get('/preco',               [FipeController::class, 'preco']);
    Route::post('/buscar-automatico',  [FipeController::class, 'buscarAutomatico']);
});
