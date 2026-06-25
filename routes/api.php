<?php

use App\Http\Controllers\Api\ActiviteController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CultureController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\FinanceController;
use App\Http\Controllers\Api\IntrantController;
use App\Http\Controllers\Api\MouvementStockController;
use App\Http\Controllers\Api\ParcelleController;
use App\Http\Controllers\Api\SyncController;
use App\Http\Controllers\Api\TestSolController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API SUUF — Groupe 1 (MVP)
|--------------------------------------------------------------------------
| Préfixe automatique : /api
*/

// ---- Public ----
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login',    [AuthController::class, 'login']);

// ---- Protégé par jeton Sanctum ----
Route::middleware('auth:sanctum')->group(function () {

    Route::get('/me',     [AuthController::class, 'me']);
    Route::post('/logout',[AuthController::class, 'logout']);

    Route::get('/dashboard', [DashboardController::class, 'index']);

    // Parcelles — les agents terrain ne peuvent pas supprimer
    Route::apiResource('parcelles', ParcelleController::class)->except(['destroy']);
    Route::delete('parcelles/{parcelle}', [ParcelleController::class, 'destroy'])
        ->middleware('role:chef_exploitation|producteur');

    Route::apiResource('cultures',  CultureController::class);
    Route::apiResource('activites', ActiviteController::class);
    Route::apiResource('intrants',  IntrantController::class);
    Route::apiResource('tests-sol', TestSolController::class);

    // Mouvements de stock (entrée / sortie) — met à jour le stock de l'intrant
    Route::get('/mouvements-stock',      [MouvementStockController::class, 'index']);
    Route::post('/mouvements-stock',     [MouvementStockController::class, 'store']);
    Route::get('/mouvements-stock/{id}', [MouvementStockController::class, 'show']);

    // Finances
    Route::get('/finances',        [FinanceController::class, 'index']);
    Route::post('/finances',       [FinanceController::class, 'store']);
    Route::delete('/finances/{id}',[FinanceController::class, 'destroy']);

    // Synchronisation hors-ligne
    Route::post('/sync/push', [SyncController::class, 'push']);
    Route::get('/sync/pull',  [SyncController::class, 'pull']);

    // Gestion des utilisateurs — chef_exploitation uniquement
    Route::middleware('role:chef_exploitation')->group(function () {
        Route::get('/users',          [UserController::class, 'index']);
        Route::put('/users/{id}',     [UserController::class, 'update']);
        Route::delete('/users/{id}',  [UserController::class, 'destroy']);
    });
});
