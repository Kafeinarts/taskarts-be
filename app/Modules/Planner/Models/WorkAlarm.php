<?php

namespace App\Modules\Planner\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['user_id', 'title', 'time', 'days', 'enabled', 'snooze', 'sound', 'note', 'last_triggered_at'])]
class WorkAlarm extends Model
{
    use HasFactory;

    /**
     * Relasi: pemilik alarm (user).
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
            'days' => 'array',
            'enabled' => 'boolean',
            'snooze' => 'boolean',
            'last_triggered_at' => 'datetime',
        ];
    }
}
