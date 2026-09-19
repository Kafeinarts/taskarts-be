<?php

namespace App\Modules\Finance\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['user_id', 'number', 'customer_name', 'customer_email', 'customer_phone', 'customer_company', 'issue_date', 'due_date', 'status', 'payment_status', 'currency', 'subtotal', 'tax', 'discount', 'total', 'amount_paid', 'items', 'notes', 'terms'])]
class Invoice extends Model
{
    use HasFactory;

    /**
     * Relasi: pemilik faktur (user).
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Hitung sisa tagihan (total dikurangi jumlah yang sudah dibayar).
     */
    public function getBalanceDueAttribute(): float
    {
        return (float) ($this->total - $this->amount_paid);
    }

    /**
     * Status pembayaran yang disimpulkan dari jumlah dibayar.
     */
    public function getPaymentStatusLabelAttribute(): string
    {
        return match (true) {
            $this->amount_paid >= $this->total && $this->total > 0 => 'paid',
            $this->amount_paid > 0 => 'partial',
            default => 'unpaid',
        };
    }

    /**
     * Atribut yang harus di-cast ke tipe data tertentu.
     */
    protected function casts(): array
    {
        return [
            'subtotal' => 'decimal:2',
            'tax' => 'decimal:2',
            'discount' => 'decimal:2',
            'total' => 'decimal:2',
            'amount_paid' => 'decimal:2',
            'items' => 'array',
            'issue_date' => 'date',
            'due_date' => 'date',
        ];
    }
}
