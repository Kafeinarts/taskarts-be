<?php

use App\Modules\Attendance\Controllers\AttendanceController;
use Illuminate\Support\Facades\Route;

Route::prefix('attendance')->middleware('auth:sanctum')->group(function (): void {
    Route::get('/', [AttendanceController::class, 'index']);
    Route::get('summary', [AttendanceController::class, 'summary']);
    Route::post('/', [AttendanceController::class, 'store']);
});
