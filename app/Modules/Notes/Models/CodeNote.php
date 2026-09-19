<?php

namespace App\Modules\Notes\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['user_id', 'title', 'content', 'language', 'tags', 'is_pinned', 'is_favorite', 'is_archived'])]
class CodeNote extends Model
{
    use HasFactory;

    /**
     * Relasi: pemilik catatan kode (user).
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
            'tags' => 'array',
            'is_pinned' => 'boolean',
            'is_favorite' => 'boolean',
            'is_archived' => 'boolean',
        ];
    }
}
