<?php

declare(strict_types=1);

namespace App\Modules\Settings\Services;

use App\Modules\Settings\Models\FeatureSetting;
use Illuminate\Database\Eloquent\Collection;

/**
 * FeatureSettingService
 *
 * Service untuk mengelola pengaturan fitur aktif/nonaktif.
 * is_enabled = 1 → aktif, is_enabled = 0 → nonaktif.
 */
class FeatureSettingService
{
    /**
     * Daftar seluruh fitur (untuk admin checklist).
     */
    public function index(): Collection
    {
        return FeatureSetting::orderBy('group_name')
            ->orderBy('sort_order')
            ->orderBy('label')
            ->get();
    }

    /**
     * Ambil hanya fitur yang aktif (is_enabled = 1) untuk sidebar user.
     */
    public function enabled(): Collection
    {
        return FeatureSetting::where('is_enabled', 1)
            ->orderBy('sort_order')
            ->get();
    }

    /**
     * Toggle status aktif/nonaktif satu fitur.
     */
    public function toggle(FeatureSetting $feature): FeatureSetting
    {
        $feature->update(['is_enabled' => $feature->is_enabled ? 0 : 1]);

        return $feature->fresh();
    }

    /**
     * Simpan status seluruh fitur sekaligus (batch update).
     *
     * @param  array<int, array{key: string, is_enabled: int}>  $items
     */
    public function sync(array $items): Collection
    {
        foreach ($items as $item) {
            FeatureSetting::where('key', $item['key'])->update([
                'is_enabled' => $item['is_enabled'] ? 1 : 0,
            ]);
        }

        return $this->index();
    }
}
