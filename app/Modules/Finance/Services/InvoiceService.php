<?php

declare(strict_types=1);

namespace App\Modules\Finance\Services;

use App\Modules\Finance\Models\Invoice;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

/**
 * InvoiceService
 *
 * Service modul Finance untuk CRUD faktur/invoice, perhitungan total,
 * status pembayaran, dan nomor unik otomatis.
 */
class InvoiceService
{
    /**
     * Daftar faktur milik user dengan filter opsional.
     *
     * @param  array<string,mixed>  $filters  status, payment_status, q
     */
    public function index(array $filters = []): Collection
    {
        $query = Invoice::query();

        if (current_user_id() !== null) {
            $query->where('user_id', current_user_id());
        }

        return $query
            ->when($filters['status'] ?? null, fn (Builder $query, string $status) => $query->where('status', $status))
            ->when($filters['payment_status'] ?? null, fn (Builder $query, string $paymentStatus) => $query->where('payment_status', $paymentStatus))
            ->when($filters['q'] ?? null, fn (Builder $query, string $q) => $query->where('number', 'like', "%{$q}%"))
            ->orderByDesc('issue_date')
            ->latest()
            ->get();
    }

    /**
     * Ambil satu faktur.
     */
    public function show(Invoice $invoice): Invoice
    {
        return $invoice;
    }

    /**
     * Buat faktur baru beserta perhitungan total otomatis.
     *
     * @param  array<string,mixed>  $data
     */
    public function store(array $data): Invoice
    {
        $data['user_id'] = current_user_id();
        $data['number'] = $data['number'] ?? generate_invoice_number();
        $data = $this->recalculate($data);

        return Invoice::create($data);
    }

    /**
     * Perbarui faktur yang sudah ada.
     *
     * @param  array<string,mixed>  $data
     */
    public function update(Invoice $invoice, array $data): Invoice
    {
        $data = $this->recalculate($data, $invoice);

        $invoice->update($data);

        return $invoice->fresh();
    }

    /**
     * Hapus faktur.
     */
    public function destroy(Invoice $invoice): void
    {
        $invoice->delete();
    }

    /**
     * Rekapitulasi faktur berdasarkan status pembayaran.
     *
     * @return array<string,mixed>
     */
    public function stats(): array
    {
        $query = Invoice::query();

        if (current_user_id() !== null) {
            $query->where('user_id', current_user_id());
        }

        return [
            'total' => money_value((clone $query)->sum('total')),
            'paid' => money_value((clone $query)->where('payment_status', 'paid')->sum('total')),
            'unpaid' => money_value((clone $query)->where('payment_status', 'unpaid')->sum('total')),
            'partial' => money_value((clone $query)->where('payment_status', 'partial')->sum('total')),
            'count' => (clone $query)->count(),
            'receivable' => money_value((clone $query)->get()->sum(fn (Invoice $invoice) => $invoice->balance_due)),
        ];
    }

    /**
     * Catat pembayaran terhadap invoice: menambah amount_paid dan
     * memperbarui payment_status secara otomatis.
     */
    public function recordPayment(Invoice $invoice, float $amount): Invoice
    {
        $invoice->update([
            'amount_paid' => money_value($invoice->amount_paid + $amount),
            'payment_status' => $amount >= $invoice->balance_due ? 'paid' : 'partial',
        ]);

        return $invoice->fresh();
    }

    /**
     * Hitung ulang subtotal, tax, discount, dan total dari items.
     *
     * @param  array<string,mixed>  $data
     * @return array<string,mixed>
     */
    protected function recalculate(array $data, ?Invoice $invoice = null): array
    {
        $items = $data['items'] ?? $invoice?->items ?? [];

        $subtotal = collect($items)->sum(fn (array $item) => money_value(($item['price'] ?? 0) * ($item['quantity'] ?? 1)));
        $discount = money_value($data['discount'] ?? 0);
        $tax = money_value($data['tax'] ?? 0);
        $total = money_value($subtotal - $discount + $tax);

        $data['subtotal'] = $subtotal;
        $data['tax'] = $tax;
        $data['discount'] = $discount;
        $data['total'] = $total;

        return $data;
    }
}
