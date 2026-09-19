<?php

use App\Modules\Planner\Controllers\EventController;
use App\Modules\Planner\Controllers\MoodLogController;
use App\Modules\Planner\Controllers\WorkAlarmController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Route Modul: Planner (Agenda, Mood, Alarm)
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->group(function (): void {
    // Event / kalender
    Route::prefix('events')->group(function (): void {
        Route::get('upcoming', [EventController::class, 'upcoming']);
        Route::get('/', [EventController::class, 'index']);
        Route::post('/', [EventController::class, 'store']);
        Route::get('{event}', [EventController::class, 'show']);
        Route::put('{event}', [EventController::class, 'update']);
        Route::patch('{event}', [EventController::class, 'update']);
        Route::delete('{event}', [EventController::class, 'destroy']);
    });

    // Mood
    Route::prefix('mood-logs')->group(function (): void {
        Route::get('summary', [MoodLogController::class, 'summary']);
        Route::get('/', [MoodLogController::class, 'index']);
        Route::post('/', [MoodLogController::class, 'store']);
        Route::delete('{log}', [MoodLogController::class, 'destroy']);
    });

    // Alarm kerja
    Route::prefix('work-alarms')->group(function (): void {
        Route::get('/', [WorkAlarmController::class, 'index']);
        Route::post('/', [WorkAlarmController::class, 'store']);
        Route::get('{alarm}', [WorkAlarmController::class, 'show']);
        Route::put('{alarm}', [WorkAlarmController::class, 'update']);
        Route::patch('{alarm}', [WorkAlarmController::class, 'update']);
        Route::delete('{alarm}', [WorkAlarmController::class, 'destroy']);
    });
});
