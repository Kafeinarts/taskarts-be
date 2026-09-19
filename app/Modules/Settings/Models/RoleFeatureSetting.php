<?php

declare(strict_types=1);

namespace App\Modules\Settings\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * RoleFeatureSetting
 *
 * Menyimpan mapping role → feature → is_enabled.
 * Digunakan untuk mengontrol fitur mana yang bisa diakses oleh setiap role.
 */
class RoleFeatureSetting extends Model
{
    protected $fillable = ['role', 'feature_key', 'is_enabled'];

    protected function casts(): array
    {
        return [
            'is_enabled' => 'boolean',
        ];
    }
}
