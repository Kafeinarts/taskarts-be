<?php

declare(strict_types=1);

namespace App\Modules\Finance\Controllers;

use App\Http\Controllers\Api\ApiController;
use App\Modules\Finance\Models\FinanceTransaction;
use App\Modules\Finance\Services\FinanceTransactionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * FinanceTransactionController
 *
 * Kontroler RESTful untuk resource transaksi keuangan modul Finance.
 */
class FinanceTransactionController extends ApiController
{
    /**
     * Daftar transaksi dengan filter opsional (type, category_id, from, to, q).
     */
    public function index(Request $request, FinanceTransactionService $service): JsonResponse
    {
        $transactions = $service->index($request->query());

        return $this->ok($transactions, 'Daftar transaksi keuangan');
    }

    /**
     * Detail satu transaksi.
     */
    public function show(FinanceTransaction $transaction, FinanceTransactionService $service): JsonResponse
    {
        return $this->ok($service->show($transaction), 'Detail transaksi');
    }

    /**
     * Buat transaksi baru.
     */
    public function store(Request $request, FinanceTransactionService $service): JsonResponse
    {
        $data = $request->validate([
            'category_id' => ['nullable', 'integer', 'exists:finance_categories,id'],
            'type' => ['required', 'string', 'in:income,expense'],
            'amount' => ['required', 'numeric', 'min:0'],
            'date' => ['required', 'date'],
            'description' => ['nullable', 'string'],
            'payment_method' => ['nullable', 'string', 'max:100'],
            'status' => ['nullable', 'string', 'in:pending,completed,failed,refunded'],
            'reference' => ['nullable', 'string', 'max:255'],
            'attachments' => ['nullable', 'array'],
        ]);

        $transaction = $service->store($data);

        return $this->ok($transaction, 'Transaksi dibuat', 201);
    }

    /**
     * Perbarui transaksi yang sudah ada.
     */
    public function update(Request $request, FinanceTransaction $transaction, FinanceTransactionService $service): JsonResponse
    {
        $data = $request->validate([
            'category_id' => ['nullable', 'integer', 'exists:finance_categories,id'],
            'type' => ['sometimes', 'string', 'in:income,expense'],
            'amount' => ['sometimes', 'numeric', 'min:0'],
            'date' => ['sometimes', 'date'],
            'description' => ['nullable', 'string'],
            'payment_method' => ['nullable', 'string', 'max:100'],
            'status' => ['nullable', 'string', 'in:pending,completed,failed,refunded'],
            'reference' => ['nullable', 'string', 'max:255'],
            'attachments' => ['nullable', 'array'],
        ]);

        $updated = $service->update($transaction, $data);

        return $this->ok($updated, 'Transaksi diperbarui');
    }

    /**
     * Hapus transaksi.
     */
    public function destroy(FinanceTransaction $transaction, FinanceTransactionService $service): JsonResponse
    {
        $service->destroy($transaction);

        return $this->ok([], 'Transaksi dihapus');
    }

    /**
     * Ringkasan pemasukan/pengeluaran/saldo pada periode tertentu.
     */
    public function summary(Request $request, FinanceTransactionService $service): JsonResponse
    {
        $summary = $service->summary($request->query());

        return $this->ok($summary, 'Ringkasan keuangan');
    }

    /**
     * Arus kas (cash flow) per hari dalam rentang tanggal.
     */
    public function cashFlow(Request $request, FinanceTransactionService $service): JsonResponse
    {
        $data = $request->validate([
            'from' => ['required', 'date'],
            'to' => ['required', 'date', 'after_or_equal:from'],
        ]);

        return $this->ok($service->cashFlow($data['from'], $data['to']), 'Arus kas harian');
    }
}
