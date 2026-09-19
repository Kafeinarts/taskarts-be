<?php

use App\Modules\Cv\Controllers\CvController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Route Modul: Cv (Resume)
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->prefix('cvs')->group(function (): void {
    Route::get('/', [CvController::class, 'index']);
    Route::post('/', [CvController::class, 'store']);
    Route::get('{cv}', [CvController::class, 'show']);
    Route::put('{cv}', [CvController::class, 'update']);
    Route::patch('{cv}', [CvController::class, 'update']);
    Route::post('{cv}/default', [CvController::class, 'setDefault']);
    Route::delete('{cv}', [CvController::class, 'destroy']);
});
