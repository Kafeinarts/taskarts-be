<?php

use App\Modules\Groups\Controllers\GroupController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Route Modul: Groups (Tim / Teams)
|--------------------------------------------------------------------------
*/

Route::prefix('groups')->group(function (): void {
    // Semua user bisa melihat daftar grup dan grup sendiri
    Route::get('/', [GroupController::class, 'index'])->middleware('auth:sanctum');
    Route::get('my', [GroupController::class, 'myGroups'])->middleware('auth:sanctum');
    Route::get('{group}', [GroupController::class, 'show'])->middleware('auth:sanctum');

    // Admin-only actions — semua di bawah auth:sanctum
    Route::middleware('auth:sanctum')->group(function (): void {
        Route::post('/', [GroupController::class, 'store']);
        Route::put('{group}', [GroupController::class, 'update']);
        Route::patch('{group}', [GroupController::class, 'update']);
        Route::delete('{group}', [GroupController::class, 'destroy']);
        Route::post('{group}/members', [GroupController::class, 'addMember']);
        Route::patch('{group}/members/{user}/role', [GroupController::class, 'updateMemberRole']);
        Route::delete('{group}/members/{user}', [GroupController::class, 'removeMember']);
    });
});
