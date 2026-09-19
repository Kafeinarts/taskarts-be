<?php

namespace App\Modules\Tasks\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['user_id', 'project_id', 'title', 'description', 'due_date', 'status', 'priority', 'category', 'tags', 'is_completed', 'completed_at', 'time_spent_minutes'])]
class Task extends Model
{
    use HasFactory;

    /**
     * Relasi: pemilik tugas (user).
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi: proyek yang menaungi tugas (opsional).
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Atribut yang harus di-cast ke tipe data tertentu.
     */
    protected function casts(): array
    {
        return [
            'tags' => 'array',
            'is_completed' => 'boolean',
            'completed_at' => 'datetime',
            'due_date' => 'date',
        ];
    }
}
