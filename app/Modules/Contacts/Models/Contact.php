<?php

namespace App\Modules\Contacts\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['user_id', 'name', 'company', 'role', 'phone', 'email', 'address', 'website', 'tags', 'notes', 'is_favorite', 'avatar'])]
class Contact extends Model
{
    use HasFactory;

    /**
     * Relasi: pemilik kontak (user).
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
            'is_favorite' => 'boolean',
        ];
    }
}
