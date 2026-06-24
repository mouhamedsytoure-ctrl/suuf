<?php

use App\Http\Controllers\Api\ActiviteController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CultureController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\FinanceController;
use App\Http\Controllers\Api\IntrantController;
use App\Http\Controllers\Api\ParcelleController;
use App\Http\Controllers\Api\SyncController;
use App\Http\Controllers\Api\TestSolController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API SUUF — Groupe 1 (MVP)
|--------------------------------------------------------------------------
| Préfixe automatique : /api
*/

// ---- Public ----
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// ---- Protégé par jeton Sanctum ----
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::get('/dashboard', [DashboardController::class, 'index']);

    Route::apiResource('parcelles', ParcelleController::class);
    Route::apiResource('cultures', CultureController::class);
    Route::apiResource('activites', ActiviteController::class);
    Route::apiResource('intrants', IntrantController::class);
    Route::apiResource('tests-sol', TestSolController::class);

    Route::get('/finances', [FinanceController::class, 'index']);
    Route::post('/finances', [FinanceController::class, 'store']);
    Route::delete('/finances/{id}', [FinanceController::class, 'destroy']);

    // Synchronisation hors-ligne
    Route::post('/sync/push', [SyncController::class, 'push']);
    Route::get('/sync/pull', [SyncController::class, 'pull']);
});
