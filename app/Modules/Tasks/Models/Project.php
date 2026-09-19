<?php

namespace App\Modules\Tasks\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['user_id', 'name', 'description', 'start_date', 'due_date', 'status', 'priority', 'color', 'budget', 'progress', 'tags', 'is_archived'])]
class Project extends Model
{
    use HasFactory;

    /**
     * Relasi: pemilik proyek (user).
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi: daftar tugas (task) milik proyek ini.
     */
    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    /**
     * Atribut yang harus di-cast ke tipe data tertentu.
     */
    protected function casts(): array
    {
        return [
            'budget' => 'decimal:2',
            'progress' => 'integer',
            'tags' => 'array',
            'is_archived' => 'boolean',
            'start_date' => 'date',
            'due_date' => 'date',
        ];
    }
}
