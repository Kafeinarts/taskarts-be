<?php

use App\Modules\Tasks\Controllers\ProjectController;
use App\Modules\Tasks\Controllers\TaskController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Route Modul: Tasks (Tugas & Proyek)
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->group(function (): void {
    // Proyek
    Route::prefix('projects')->group(function (): void {
        Route::get('stats', [ProjectController::class, 'stats']);
        Route::get('/', [ProjectController::class, 'index']);
        Route::post('/', [ProjectController::class, 'store']);
        Route::get('{project}', [ProjectController::class, 'show']);
        Route::put('{project}', [ProjectController::class, 'update']);
        Route::patch('{project}', [ProjectController::class, 'update']);
        Route::delete('{project}', [ProjectController::class, 'destroy']);
    });

    // Tugas
    Route::prefix('tasks')->group(function (): void {
        Route::get('stats', [TaskController::class, 'stats']);
        Route::get('/', [TaskController::class, 'index']);
        Route::post('/', [TaskController::class, 'store']);
        Route::get('{task}', [TaskController::class, 'show']);
        Route::put('{task}', [TaskController::class, 'update']);
        Route::patch('{task}', [TaskController::class, 'update']);
        Route::post('{task}/complete', [TaskController::class, 'complete']);
        Route::post('{task}/reopen', [TaskController::class, 'reopen']);
        Route::delete('{task}', [TaskController::class, 'destroy']);
    });
});
