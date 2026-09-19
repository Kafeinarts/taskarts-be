<?php

declare(strict_types=1);

namespace App\Modules\Settings\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * FeatureSetting
 *
 * Model untuk pengaturan fitur aktif/nonaktif.
 * Admin dapat mengaktifkan/menonaktifkan menu agar user hanya melihat fitur yang diizinkan.
 */
#[Fillable(['key', 'label', 'description', 'is_enabled', 'group_name', 'sort_order'])]
class FeatureSetting extends Model
{
    use HasFactory;

    /**
     * Ambil semua fitur yang aktif (is_enabled = 1).
     */
    public static function enabledKeys(): array
    {
        return static::where('is_enabled', 1)->pluck('key')->toArray();
    }
}
