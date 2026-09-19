<?php

namespace App\Modules\Planner\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['user_id', 'mood', 'mood_label', 'score', 'note', 'logged_at'])]
class MoodLog extends Model
{
    use HasFactory;

    /**
     * Relasi: pemilik log mood (user).
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
            'score' => 'integer',
            'logged_at' => 'datetime',
        ];
    }
}
