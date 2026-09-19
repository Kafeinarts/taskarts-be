<?php

declare(strict_types=1);

namespace App\Modules\Groups\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Group
 *
 * Model untuk grup/teams (misal: TIM IT, TIM BUSINESS, TIM DEVELOPER).
 * Relasi many-to-many dengan User melalui pivot user_groups.
 */
#[Fillable(['name', 'description'])]
class Group extends Model
{
    use HasFactory;

    /**
     * Anggota yang tergabung dalam grup ini.
     */
    public function members(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_groups')
            ->withPivot('role')
            ->withTimestamps();
    }

    /**
     * Hitung jumlah anggota aktif.
     */
    public function getMemberCountAttribute(): int
    {
        return $this->members()->count();
    }
}
