<?php

namespace App\Modules\Finance\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['user_id', 'type', 'contact_name', 'description', 'amount', 'paid_amount', 'due_date', 'status', 'settled_at'])]
class ApArEntry extends Model
{
    use HasFactory;

    /**
     * Relasi: pemilik entri (user).
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Hitung sisa nilai yang belum dibayar / ditagih.
     */
    public function getBalanceDueAttribute(): float
    {
        return (float) ($this->amount - $this->paid_amount);
    }

    /**
     * Atribut yang harus di-cast ke tipe data tertentu.
     */
    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'paid_amount' => 'decimal:2',
            'due_date' => 'date',
            'settled_at' => 'datetime',
        ];
    }
}
