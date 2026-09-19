<?php

namespace App\Modules\Finance\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['user_id', 'name', 'type', 'category', 'unit', 'quantity', 'unit_price', 'amount', 'notes'])]
class RabItem extends Model
{
    use HasFactory;

    /**
     * Relasi: pemilik item RAB (user).
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Hitung total nilai per item (qty x harga satuan) bila perlu.
     */
    public function getCalculatedAmountAttribute(): float
    {
        return (float) ($this->quantity * $this->unit_price);
    }

    /**
     * Atribut yang harus di-cast ke tipe data tertentu.
     */
    protected function casts(): array
    {
        return [
            'quantity' => 'decimal:2',
            'unit_price' => 'decimal:2',
            'amount' => 'decimal:2',
        ];
    }
}
