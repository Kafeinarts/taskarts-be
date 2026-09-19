<?php

namespace App\Modules\Habits\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['habit_id', 'log_date', 'value', 'notes'])]
class HabitLog extends Model
{
    use HasFactory;

    /**
     * Relasi: kebiasaan yang dicatat pada log ini.
     */
    public function habit(): BelongsTo
    {
        return $this->belongsTo(Habit::class);
    }

    /**
     * Atribut yang harus di-cast ke tipe data tertentu.
     */
    protected function casts(): array
    {
        return [
            'value' => 'decimal:2',
            'log_date' => 'date',
        ];
    }
}
