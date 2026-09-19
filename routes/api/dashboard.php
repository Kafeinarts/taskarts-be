<?php

use App\Modules\Dashboard\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Route Modul: Dashboard
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->prefix('dashboard')->group(function (): void {
    Route::get('/', [DashboardController::class, 'overview']);
});
