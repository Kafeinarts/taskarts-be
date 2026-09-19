<?php

use App\Modules\Settings\Controllers\FeatureSettingController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Route Modul: Feature Settings (Toggle Menu — Global & Per-User)
|--------------------------------------------------------------------------
*/

Route::prefix('features')->group(function (): void {
    // Global feature settings
    Route::get('/', [FeatureSettingController::class, 'index'])->middleware('auth:sanctum');
    Route::get('enabled', [FeatureSettingController::class, 'enabled'])->middleware('auth:sanctum');
    Route::patch('{feature}/toggle', [FeatureSettingController::class, 'toggle'])->middleware('auth:sanctum');
    Route::post('sync', [FeatureSettingController::class, 'sync'])->middleware('auth:sanctum');

    // Per-user feature settings (admin only)
    Route::get('user/{user}', [FeatureSettingController::class, 'userFeatures'])->middleware('auth:sanctum');
    Route::get('user/{user}/enabled', [FeatureSettingController::class, 'userEnabled'])->middleware('auth:sanctum');
    Route::post('user/{user}/sync', [FeatureSettingController::class, 'syncUserFeatures'])->middleware('auth:sanctum');

    // Per-role feature settings (admin only)
    Route::get('roles', [FeatureSettingController::class, 'roleFeatures'])->middleware('auth:sanctum');
    Route::get('roles/{role}/enabled', [FeatureSettingController::class, 'roleEnabled'])->middleware('auth:sanctum');
    Route::post('roles/sync', [FeatureSettingController::class, 'syncRoleFeatures'])->middleware('auth:sanctum');
});
