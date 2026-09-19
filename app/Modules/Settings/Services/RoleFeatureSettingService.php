<?php

declare(strict_types=1);

namespace App\Modules\Settings\Services;

use App\Modules\Settings\Models\RoleFeatureSetting;
use Illuminate\Database\Eloquent\Collection;

/**
 * RoleFeatureSettingService
 *
 * Mengelola fitur per-role: role mana mendapat akses fitur apa.
 */
class RoleFeatureSettingService
{
    /**
     * Ambil seluruh mapping role → features (admin view).
     */
    public function index(): Collection
    {
        return RoleFeatureSetting::orderBy('role')
            ->orderBy('feature_key')
            ->get();
    }

    /**
     * Ambil hanya fitur yang aktif untuk role tertentu.
     */
    public function enabledForRole(string $role): array
    {
        return RoleFeatureSetting::where('role', $role)
            ->where('is_enabled', true)
            ->pluck('feature_key')
            ->toArray();
    }

    /**
     * Sync mapping role → features sekaligus.
     *
     * @param  array<int, array{role: string, feature_key: string, is_enabled: bool}>  $items
     */
    public function sync(array $items): Collection
    {
        foreach ($items as $item) {
            RoleFeatureSetting::updateOrCreate(
                ['role' => $item['role'], 'feature_key' => $item['feature_key']],
                ['is_enabled' => $item['is_enabled']],
            );
        }

        return $this->index();
    }

    /**
     * Hapus semua mapping untuk role tertentu.
     */
    public function clearRole(string $role): void
    {
        RoleFeatureSetting::where('role', $role)->delete();
    }
}
