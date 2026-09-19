<?php

declare(strict_types=1);

namespace App\Modules\Finance\Services;

use App\Modules\Finance\Models\ApArEntry;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

/**
 * ApArService
 *
 * Service modul Finance untuk mengelola entri hutang (Accounts Payable)
 * dan piutang (Accounts Receivable), termasuk pelunasan.
 */
class ApArService
{
    /**
     * Daftar entri AP/AR milik user dengan filter tipe/status.
     *
     * @param  array<string,mixed>  $filters  type, status
     */
    public function index(array $filters = []): Collection
    {
        $query = ApArEntry::query();

        if (current_user_id() !== null) {
            $query->where('user_id', current_user_id());
        }

        return $query
            ->when($filters['type'] ?? null, fn (Builder $query, string $type) => $query->where('type', $type))
            ->when($filters['status'] ?? null, fn (Builder $query, string $status) => $query->where('status', $status))
            ->orderByDesc('due_date')
            ->latest()
            ->get();
    }

    /**
     * Ambil satu entri AP/AR.
     */
    public function show(ApArEntry $entry): ApArEntry
    {
        return $entry;
    }

    /**
     * Buat entri AP/AR baru.
     *
     * @param  array<string,mixed>  $data
     */
    public function store(array $data): ApArEntry
    {
        $data['user_id'] = current_user_id();
        $data['amount'] = money_value($data['amount'] ?? 0);
        $data['paid_amount'] = money_value($data['paid_amount'] ?? 0);

        return ApArEntry::create($data);
    }

    /**
     * Perbarui entri AP/AR yang sudah ada.
     *
     * @param  array<string,mixed>  $data
     */
    public function update(ApArEntry $entry, array $data): ApArEntry
    {
        if (array_key_exists('amount', $data)) {
            $data['amount'] = money_value($data['amount']);
        }

        $entry->update($data);

        return $entry->fresh();
    }

    /**
     * Hapus entri AP/AR.
     */
    public function destroy(ApArEntry $entry): void
    {
        $entry->delete();
    }

    /**
     * Catat pembayaran/penagihan parsial dan tandai settled bila lunas.
     */
    public function settle(ApArEntry $entry, float $amount): ApArEntry
    {
        $paid = money_value($entry->paid_amount + $amount);

        $entry->update([
            'paid_amount' => $paid,
            'status' => $paid >= $entry->amount ? 'settled' : 'partial',
            'settled_at' => $paid >= $entry->amount ? now() : null,
        ]);

        return $entry->fresh();
    }

    /**
     * Ringkasan saldo hutang (payable) dan piutang (receivable).
     *
     * @return array<string,mixed>
     */
    public function summary(): array
    {
        $base = ApArEntry::query();

        if (current_user_id() !== null) {
            $base->where('user_id', current_user_id());
        }

        $receivable = (clone $base)->where('type', 'receivable')->where('status', '!=', 'settled')->get();
        $payable = (clone $base)->where('type', 'payable')->where('status', '!=', 'settled')->get();

        return [
            'receivable_total' => money_value($receivable->sum('amount')),
            'receivable_outstanding' => money_value($receivable->sum(fn (ApArEntry $entry) => $entry->balance_due)),
            'payable_total' => money_value($payable->sum('amount')),
            'payable_outstanding' => money_value($payable->sum(fn (ApArEntry $entry) => $entry->balance_due)),
            'net_position' => money_value(
                $receivable->sum(fn (ApArEntry $entry) => $entry->balance_due)
                - $payable->sum(fn (ApArEntry $entry) => $entry->balance_due),
            ),
        ];
    }
}
