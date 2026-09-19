<?php

use App\Modules\Finance\Controllers\ApArController;
use App\Modules\Finance\Controllers\BudgetController;
use App\Modules\Finance\Controllers\FinanceCategoryController;
use App\Modules\Finance\Controllers\FinanceTransactionController;
use App\Modules\Finance\Controllers\InvoiceController;
use App\Modules\Finance\Controllers\RabController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Route Modul: Finance (Keuangan)
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->prefix('finance')->group(function (): void {
    // Kategori
    Route::prefix('categories')->group(function (): void {
        Route::get('/', [FinanceCategoryController::class, 'index']);
        Route::post('/', [FinanceCategoryController::class, 'store']);
        Route::get('{category}', [FinanceCategoryController::class, 'show']);
        Route::put('{category}', [FinanceCategoryController::class, 'update']);
        Route::patch('{category}', [FinanceCategoryController::class, 'update']);
        Route::delete('{category}', [FinanceCategoryController::class, 'destroy']);
    });

    // Transaksi
    Route::prefix('transactions')->group(function (): void {
        Route::get('summary', [FinanceTransactionController::class, 'summary']);
        Route::get('cash-flow', [FinanceTransactionController::class, 'cashFlow']);
        Route::get('/', [FinanceTransactionController::class, 'index']);
        Route::post('/', [FinanceTransactionController::class, 'store']);
        Route::get('{transaction}', [FinanceTransactionController::class, 'show']);
        Route::put('{transaction}', [FinanceTransactionController::class, 'update']);
        Route::patch('{transaction}', [FinanceTransactionController::class, 'update']);
        Route::delete('{transaction}', [FinanceTransactionController::class, 'destroy']);
    });

    // Anggaran
    Route::prefix('budgets')->group(function (): void {
        Route::get('/', [BudgetController::class, 'index']);
        Route::post('/', [BudgetController::class, 'store']);
        Route::get('{budget}', [BudgetController::class, 'show']);
        Route::put('{budget}', [BudgetController::class, 'update']);
        Route::patch('{budget}', [BudgetController::class, 'update']);
        Route::post('{budget}/sync-spent', [BudgetController::class, 'syncSpent']);
        Route::get('{budget}/usage', [BudgetController::class, 'usage']);
        Route::delete('{budget}', [BudgetController::class, 'destroy']);
    });

    // Invoice
    Route::prefix('invoices')->group(function (): void {
        Route::get('stats', [InvoiceController::class, 'stats']);
        Route::get('/', [InvoiceController::class, 'index']);
        Route::post('/', [InvoiceController::class, 'store']);
        Route::get('{invoice}', [InvoiceController::class, 'show']);
        Route::put('{invoice}', [InvoiceController::class, 'update']);
        Route::patch('{invoice}', [InvoiceController::class, 'update']);
        Route::post('{invoice}/pay', [InvoiceController::class, 'pay']);
        Route::get('{invoice}/pdf', [InvoiceController::class, 'pdf']);
        Route::delete('{invoice}', [InvoiceController::class, 'destroy']);
    });

    // Hutang / Piutang
    Route::prefix('ap-ar')->group(function (): void {
        Route::get('summary', [ApArController::class, 'summary']);
        Route::get('/', [ApArController::class, 'index']);
        Route::post('/', [ApArController::class, 'store']);
        Route::get('{entry}', [ApArController::class, 'show']);
        Route::put('{entry}', [ApArController::class, 'update']);
        Route::patch('{entry}', [ApArController::class, 'update']);
        Route::post('{entry}/settle', [ApArController::class, 'settle']);
        Route::delete('{entry}', [ApArController::class, 'destroy']);
    });

    // RAB
    Route::prefix('rab')->group(function (): void {
        Route::get('summary', [RabController::class, 'summary']);
        Route::get('/', [RabController::class, 'index']);
        Route::post('/', [RabController::class, 'store']);
        Route::get('{item}', [RabController::class, 'show']);
        Route::put('{item}', [RabController::class, 'update']);
        Route::patch('{item}', [RabController::class, 'update']);
        Route::delete('{item}', [RabController::class, 'destroy']);
    });
});
