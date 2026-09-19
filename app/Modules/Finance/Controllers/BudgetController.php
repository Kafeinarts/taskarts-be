<?php

declare(strict_types=1);

namespace App\Modules\Finance\Controllers;

use App\Http\Controllers\Api\ApiController;
use App\Modules\Finance\Models\Budget;
use App\Modules\Finance\Services\BudgetService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * BudgetController
 *
 * Kontroler RESTful untuk resource anggaran (budget) modul Finance.
 */
class BudgetController extends ApiController
{
    /**
     * Daftar anggaran, opsional filter status aktif.
     */
    public function index(Request $request, BudgetService $service): JsonResponse
    {
        $active = $request->has('active') ? normalize_boolean($request->query('active')) : null;

        return $this->ok($service->index($active), 'Daftar anggaran');
    }

    /**
     * Detail satu anggaran.
     */
    public function show(Budget $budget, BudgetService $service): JsonResponse
    {
        return $this->ok($service->show($budget), 'Detail anggaran');
    }

    /**
     * Buat anggaran baru.
     */
    public function store(Request $request, BudgetService $service): JsonResponse
    {
        $data = $request->validate([
            'category_id' => ['nullable', 'integer', 'exists:finance_categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'min:0'],
            'spent' => ['nullable', 'numeric', 'min:0'],
            'period' => ['nullable', 'string', 'in:daily,weekly,monthly,yearly'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date'],
            'notes' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $budget = $service->store($data);

        return $this->ok($budget, 'Anggaran dibuat', 201);
    }

    /**
     * Perbarui anggaran yang sudah ada.
     */
    public function update(Request $request, Budget $budget, BudgetService $service): JsonResponse
    {
        $data = $request->validate([
            'category_id' => ['nullable', 'integer', 'exists:finance_categories,id'],
            'name' => ['sometimes', 'string', 'max:255'],
            'amount' => ['sometimes', 'numeric', 'min:0'],
            'period' => ['nullable', 'string', 'in:daily,weekly,monthly,yearly'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date'],
            'notes' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $updated = $service->update($budget, $data);

        return $this->ok($updated, 'Anggaran diperbarui');
    }

    /**
     * Hapus anggaran.
     */
    public function destroy(Budget $budget, BudgetService $service): JsonResponse
    {
        $service->destroy($budget);

        return $this->ok([], 'Anggaran dihapus');
    }

    /**
     * Sinkronkan realisasi pengeluaran (spent) dari transaksi.
     */
    public function syncSpent(Budget $budget, BudgetService $service): JsonResponse
    {
        $budget = $service->syncSpent($budget);

        return $this->ok($budget, 'Realisasi anggaran disinkronkan');
    }

    /**
     * Ringkasan penggunaan anggaran (sisa & persentase).
     */
    public function usage(Budget $budget, BudgetService $service): JsonResponse
    {
        return $this->ok($service->usage($budget), 'Penggunaan anggaran');
    }
}
