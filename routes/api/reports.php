<?php

use App\Modules\Reports\Controllers\ReportController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Route Modul: Reports (Ekspor Excel & PDF)
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->prefix('reports')->group(function (): void {
    Route::get('tasks/excel', [ReportController::class, 'tasksExcel']);
    Route::get('tasks/pdf', [ReportController::class, 'tasksPdf']);
    Route::get('contacts/excel', [ReportController::class, 'contactsExcel']);
    Route::get('contacts/pdf', [ReportController::class, 'contactsPdf']);
    Route::get('finance/excel', [ReportController::class, 'financeExcel']);
    Route::get('finance/pdf', [ReportController::class, 'financePdf']);
    Route::get('rab/excel', [ReportController::class, 'rabExcel']);
});
