<?php

namespace App\Modules\Habits\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['user_id', 'name', 'description', 'frequency', 'category', 'color', 'icon', 'is_active', 'streak', 'target', 'goal_type'])]
class Habit extends Model
{
    use HasFactory;

    /**
     * Relasi: pemilik kebiasaan (user).
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi: log harian pencapaian kebiasaan.
     */
    public function logs(): HasMany
    {
        return $this->hasMany(HabitLog::class);
    }

    /**
     * Atribut yang harus di-cast ke tipe data tertentu.
     */
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'streak' => 'integer',
            'target' => 'decimal:2',
        ];
    }
}
