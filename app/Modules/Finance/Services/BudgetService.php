<?php

declare(strict_types=1);

namespace App\Modules\Finance\Services;

use App\Modules\Finance\Models\Budget;
use App\Modules\Finance\Models\FinanceTransaction;
use Illuminate\Support\Collection;

/**
 * BudgetService
 *
 * Service modul Finance untuk CRUD anggaran dan sinkronisasi realisasi
 * pengeluaran (spent) dari transaksi.
 */
class BudgetService
{
    /**
     * Daftar anggaran milik user, opsional filter berdasarkan status aktif.
     */
    public function index(?bool $active = null): Collection
    {
        $query = Budget::query();

        if (current_user_id() !== null) {
            $query->where('user_id', current_user_id());
        }

        if ($active !== null) {
            $query->where('is_active', $active);
        }

        return $query->with('category:id,name,color')->orderBy('created_at')->get();
    }

    /**
     * Ambil satu anggaran.
     */
    public function show(Budget $budget): Budget
    {
        $budget->load('category:id,name,color');

        return $budget;
    }

    /**
     * Buat anggaran baru.
     *
     * @param  array<string,mixed>  $data
     */
    public function store(array $data): Budget
    {
        $data['user_id'] = current_user_id();
        $data['amount'] = money_value($data['amount'] ?? 0);
        $data['spent'] = money_value($data['spent'] ?? 0);

        return Budget::create($data);
    }

    /**
     * Perbarui anggaran yang sudah ada.
     *
     * @param  array<string,mixed>  $data
     */
    public function update(Budget $budget, array $data): Budget
    {
        if (array_key_exists('amount', $data)) {
            $data['amount'] = money_value($data['amount']);
        }

        $budget->update($data);

        return $budget->fresh();
    }

    /**
     * Hapus anggaran.
     */
    public function destroy(Budget $budget): void
    {
        $budget->delete();
    }

    /**
     * Sinkronkan nilai `spent` anggaran dengan total transaksi pengeluaran
     * pada rentang tanggal anggaran tersebut.
     */
    public function syncSpent(Budget $budget): Budget
    {
        $query = FinanceTransaction::query()->where('type', 'expense');

        if ($budget->category_id !== null) {
            $query->where('category_id', $budget->category_id);
        }
        if ($budget->start_date !== null) {
            $query->whereDate('date', '>=', $budget->start_date);
        }
        if ($budget->end_date !== null) {
            $query->whereDate('date', '<=', $budget->end_date);
        }

        $budget->update(['spent' => money_value($query->sum('amount'))]);

        return $budget->fresh();
    }

    /**
     * Ringkasan penggunaan anggaran: sisa, dan persentase terpakai.
     *
     * @return array<string,mixed>
     */
    public function usage(Budget $budget): array
    {
        $percentage = $budget->amount > 0 ? round(($budget->spent / $budget->amount) * 100, 2) : 0.0;

        return [
            'amount' => money_value($budget->amount),
            'spent' => money_value($budget->spent),
            'remaining' => money_value($budget->amount - $budget->spent),
            'percentage' => $percentage,
        ];
    }
}
