<?php

use App\Modules\Settings\Controllers\SettingsController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Route Modul: Settings
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->prefix('settings')->group(function (): void {
    Route::get('/', [SettingsController::class, 'index']);
    Route::get('group/{group}', [SettingsController::class, 'group']);
    Route::post('batch', [SettingsController::class, 'storeBatch']);
    Route::get('{key}', [SettingsController::class, 'show']);
    Route::post('{key}', [SettingsController::class, 'store']);
    Route::delete('{key}', [SettingsController::class, 'destroy']);
});
