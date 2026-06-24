<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json([
        'app' => 'SUUF API',
        'status' => 'ok',
        'docs' => 'Voir routes/api.php — préfixe /api',
    ]);
});
