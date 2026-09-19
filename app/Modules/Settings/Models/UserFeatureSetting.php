<?php

declare(strict_types=1);

namespace App\Modules\Settings\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * UserFeatureSetting
 *
 * Pengaturan fitur per-user (per-user feature gate).
 * is_enabled = 1 → aktif, is_enabled = 0 → nonaktif untuk user tertentu.
 */
#[Fillable(['user_id', 'feature_key', 'is_enabled'])]
class UserFeatureSetting extends Model
{
    use HasFactory;

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
