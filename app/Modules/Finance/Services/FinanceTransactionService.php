<?php

declare(strict_types=1);

namespace App\Modules\Finance\Services;

use App\Modules\Finance\Models\FinanceTransaction;
use App\Services\BaseService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

/**
 * FinanceTransactionService
 *
 * Service modul Finance untuk CRUD transaksi serta ringkasan keuangan
 * (income, expense, saldo) berdasarkan periode.
 */
class FinanceTransactionService extends BaseService
{
    /**
     * Daftar transaksi milik user dengan filter opsional.
     *
     * @param  array<string,mixed>  $filters  type, category_id, from, to, q
     */
    public function index(array $filters = []): Collection
    {
        return $this->own(FinanceTransaction::query())
            ->with('category:id,name,color,icon')
            ->when($filters['type'] ?? null, fn (Builder $query, string $type) => $query->where('type', $type))
            ->when($filters['category_id'] ?? null, fn (Builder $query, int $categoryId) => $query->where('category_id', $categoryId))
            ->when($filters['from'] ?? null, fn (Builder $query, string $from) => $query->whereDate('date', '>=', $from))
            ->when($filters['to'] ?? null, fn (Builder $query, string $to) => $query->whereDate('date', '<=', $to))
            ->when($filters['q'] ?? null, fn (Builder $query, string $q) => $query->where('description', 'like', "%{$q}%"))
            ->orderByDesc('date')
            ->latest()
            ->get();
    }

    /**
     * Ambil satu transaksi.
     */
    public function show(FinanceTransaction $transaction): FinanceTransaction
    {
        $transaction->load('category:id,name,color,icon');

        return $transaction;
    }

    /**
     * Buat transaksi baru.
     *
     * @param  array<string,mixed>  $data
     */
    public function store(array $data): FinanceTransaction
    {
        $data['user_id'] = $this->userId();
        $data['amount'] = $this->money($data['amount'] ?? 0);

        return FinanceTransaction::create($data);
    }

    /**
     * Perbarui transaksi yang sudah ada.
     *
     * @param  array<string,mixed>  $data
     */
    public function update(FinanceTransaction $transaction, array $data): FinanceTransaction
    {
        if (array_key_exists('amount', $data)) {
            $data['amount'] = $this->money($data['amount']);
        }

        $transaction->update($data);

        return $transaction->fresh();
    }

    /**
     * Hapus transaksi.
     */
    public function destroy(FinanceTransaction $transaction): void
    {
        $transaction->delete();
    }

    /**
     * Ringkasan keuangan periode tertentu (default: bulan berjalan).
     *
     * @param  array<string,mixed>  $filters  from, to
     * @return array<string,mixed>
     */
    public function summary(array $filters = []): array
    {
        $fromValue = $filters['from'] ?? null;
        $toValue = $filters['to'] ?? null;

        $from = $fromValue instanceof \DateTimeInterface
            ? $fromValue->toDateString()
            : ($fromValue ?: now()->startOfMonth()->toDateString());

        $to = $toValue instanceof \DateTimeInterface
            ? $toValue->toDateString()
            : ($toValue ?: now()->endOfMonth()->toDateString());

        $transactions = $this->own(FinanceTransaction::query())
            ->whereBetween('date', [$from, $to])
            ->get();

        $income = money_value($transactions->where('type', 'income')->sum('amount'));
        $expense = money_value($transactions->where('type', 'expense')->sum('amount'));

        return [
            'income' => $income,
            'expense' => $expense,
            'balance' => money_value($income - $expense),
            'count' => $transactions->count(),
            'from' => $from,
            'to' => $to,
        ];
    }

    /**
     * Arus kas (cash flow) per hari dalam rentang tanggal.
     *
     * @return array<int, array<string,mixed>>
     */
    public function cashFlow(string $from, string $to): array
    {
        $transactions = $this->own(FinanceTransaction::query())
            ->whereBetween('date', [$from, $to])
            ->get();

        return $transactions
            ->groupBy('date')
            ->map(function (Collection $items, string $date): array {
                $income = money_value($items->where('type', 'income')->sum('amount'));
                $expense = money_value($items->where('type', 'expense')->sum('amount'));

                return [
                    'date' => $date,
                    'income' => $income,
                    'expense' => $expense,
                    'balance' => money_value($income - $expense),
                ];
            })
            ->values()
            ->all();
    }
}
