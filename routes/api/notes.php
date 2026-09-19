<?php

use App\Modules\Notes\Controllers\CodeNoteController;
use App\Modules\Notes\Controllers\DiaryController;
use App\Modules\Notes\Controllers\DraftController;
use App\Modules\Notes\Controllers\NoteController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Route Modul: Notes (Catatan, Kode, Jurnal, Draf)
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->group(function (): void {
    // Catatan umum / sticky notes
    Route::prefix('notes')->group(function (): void {
        Route::get('/', [NoteController::class, 'index']);
        Route::post('/', [NoteController::class, 'store']);
        Route::get('{note}', [NoteController::class, 'show']);
        Route::put('{note}', [NoteController::class, 'update']);
        Route::patch('{note}', [NoteController::class, 'update']);
        Route::delete('{note}', [NoteController::class, 'destroy']);
    });

    // Catatan kode
    Route::prefix('code-notes')->group(function (): void {
        Route::get('/', [CodeNoteController::class, 'index']);
        Route::post('/', [CodeNoteController::class, 'store']);
        Route::get('{note}', [CodeNoteController::class, 'show']);
        Route::put('{note}', [CodeNoteController::class, 'update']);
        Route::patch('{note}', [CodeNoteController::class, 'update']);
        Route::delete('{note}', [CodeNoteController::class, 'destroy']);
    });

    // Jurnal
    Route::prefix('diary')->group(function (): void {
        Route::get('/', [DiaryController::class, 'index']);
        Route::post('/', [DiaryController::class, 'store']);
        Route::get('{entry}', [DiaryController::class, 'show']);
        Route::put('{entry}', [DiaryController::class, 'update']);
        Route::patch('{entry}', [DiaryController::class, 'update']);
        Route::delete('{entry}', [DiaryController::class, 'destroy']);
    });

    // Draf tulisan
    Route::prefix('drafts')->group(function (): void {
        Route::get('/', [DraftController::class, 'index']);
        Route::post('/', [DraftController::class, 'store']);
        Route::get('{draft}', [DraftController::class, 'show']);
        Route::put('{draft}', [DraftController::class, 'update']);
        Route::patch('{draft}', [DraftController::class, 'update']);
        Route::delete('{draft}', [DraftController::class, 'destroy']);
    });
});
