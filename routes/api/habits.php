<?php

use App\Modules\Habits\Controllers\HabitController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Route Modul: Habits (Kebiasaan)
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->prefix('habits')->group(function (): void {
    Route::get('/', [HabitController::class, 'index']);
    Route::post('/', [HabitController::class, 'store']);
    Route::get('{habit}', [HabitController::class, 'show']);
    Route::put('{habit}', [HabitController::class, 'update']);
    Route::patch('{habit}', [HabitController::class, 'update']);
    Route::post('{habit}/log', [HabitController::class, 'log']);
    Route::delete('{habit}/log', [HabitController::class, 'unlog']);
    Route::delete('{habit}', [HabitController::class, 'destroy']);
});
