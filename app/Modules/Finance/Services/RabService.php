<?php

declare(strict_types=1);

namespace App\Modules\Finance\Services;

use App\Modules\Finance\Models\RabItem;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

/**
 * RabService
 *
 * Service modul Finance untuk mengelola item Rencana Anggaran Biaya (RAB):
 * pemasukan (income), pengeluaran (expense), dan item pendukung.
 */
class RabService
{
    /**
     * Daftar item RAB milik user dengan filter tipe/kategori.
     *
     * @param  array<string,mixed>  $filters  type, category
     */
    public function index(array $filters = []): Collection
    {
        $query = RabItem::query();

        if (current_user_id() !== null) {
            $query->where('user_id', current_user_id());
        }

        return $query
            ->when($filters['type'] ?? null, fn (Builder $query, string $type) => $query->where('type', $type))
            ->when($filters['category'] ?? null, fn (Builder $query, string $category) => $query->where('category', $category))
            ->orderByDesc('created_at')
            ->get();
    }

    /**
     * Ambil satu item RAB.
     */
    public function show(RabItem $item): RabItem
    {
        return $item;
    }

    /**
     * Buat item RAB baru (nilai amount otomatis = qty x unit_price bila kosong).
     *
     * @param  array<string,mixed>  $data
     */
    public function store(array $data): RabItem
    {
        $data['user_id'] = current_user_id();
        $data['quantity'] = (float) ($data['quantity'] ?? 1);
        $data['unit_price'] = money_value($data['unit_price'] ?? 0);
        $data['amount'] = money_value($data['amount'] ?? ($data['quantity'] * $data['unit_price']));

        return RabItem::create($data);
    }

    /**
     * Perbarui item RAB yang sudah ada.
     *
     * @param  array<string,mixed>  $data
     */
    public function update(RabItem $item, array $data): RabItem
    {
        if (array_key_exists('quantity', $data)) {
            $data['quantity'] = (float) $data['quantity'];
        }
        if (array_key_exists('unit_price', $data)) {
            $data['unit_price'] = money_value($data['unit_price']);
        }
        if (array_key_exists('amount', $data)) {
            $data['amount'] = money_value($data['amount']);
        }

        $item->update($data);

        return $item->fresh();
    }

    /**
     * Hapus item RAB.
     */
    public function destroy(RabItem $item): void
    {
        $item->delete();
    }

    /**
     * Ringkasan total pemasukan, pengeluaran, dan selisih RAB.
     *
     * @return array<string,mixed>
     */
    public function summary(): array
    {
        $base = RabItem::query();

        if (current_user_id() !== null) {
            $base->where('user_id', current_user_id());
        }

        $income = money_value((clone $base)->where('type', 'income')->sum('amount'));
        $expense = money_value((clone $base)->where('type', 'expense')->sum('amount'));

        return [
            'income' => $income,
            'expense' => $expense,
            'balance' => money_value($income - $expense),
            'count' => (clone $base)->count(),
        ];
    }
}
