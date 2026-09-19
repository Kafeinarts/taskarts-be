<?php

use App\Modules\Contacts\Controllers\ContactController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Route Modul: Contacts
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->prefix('contacts')->group(function (): void {
    Route::get('stats', [ContactController::class, 'stats']);
    Route::get('/', [ContactController::class, 'index']);
    Route::post('/', [ContactController::class, 'store']);
    Route::get('{contact}', [ContactController::class, 'show']);
    Route::put('{contact}', [ContactController::class, 'update']);
    Route::patch('{contact}', [ContactController::class, 'update']);
    Route::delete('{contact}', [ContactController::class, 'destroy']);
});
