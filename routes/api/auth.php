<?php

use App\Modules\Auth\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Route Modul: Auth
|--------------------------------------------------------------------------
*/

Route::prefix('auth')->group(function (): void {
    Route::post('register', [AuthController::class, 'register'])->middleware('throttle:auth');
    Route::post('login', [AuthController::class, 'login'])->middleware('throttle:login');
    Route::post('forgot-password', [AuthController::class, 'forgotPassword']);

    Route::middleware('auth:sanctum')->group(function (): void {
        Route::post('logout', [AuthController::class, 'logout']);
        Route::get('me', [AuthController::class, 'me']);
        Route::put('profile', [AuthController::class, 'updateProfile']);
        Route::post('register-features', [AuthController::class, 'registerWithFeatures']);

        // User management (admin)
        Route::get('users', [AuthController::class, 'users']);
        Route::get('users/{id}', [AuthController::class, 'showUser']);
        Route::put('users/{id}', [AuthController::class, 'updateUser']);
        Route::put('users/{id}/features', [AuthController::class, 'updateUserFeatures']);
        Route::delete('users/{id}', [AuthController::class, 'deleteUser']);
    });
});
