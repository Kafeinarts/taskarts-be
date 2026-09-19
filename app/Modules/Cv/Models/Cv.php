<?php

namespace App\Modules\Cv\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['user_id', 'name', 'data', 'selected_template', 'custom_color', 'is_default'])]
class Cv extends Model
{
    use HasFactory;

    /**
     * Relasi: pemilik CV (user).
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Atribut yang harus di-cast ke tipe data tertentu.
     */
    protected function casts(): array
    {
        return [
            'data' => 'array',
            'is_default' => 'boolean',
        ];
    }
}
