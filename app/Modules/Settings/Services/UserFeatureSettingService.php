<?php

declare(strict_types=1);

namespace App\Modules\Settings\Services;

use App\Modules\Settings\Models\FeatureSetting;
use App\Modules\Settings\Models\UserFeatureSetting;

/**
 * UserFeatureSettingService
 *
 * Kelola fitur aktif/nonaktif per-user.
 * Jika user belum punya record, fitur global (feature_settings.is_enabled) yang berlaku.
 */
class UserFeatureSettingService
{
    /**
     * Ambil semua fitur beserta status per-user.
     * Jika user belum punya customisasi, gunakan nilai global.
     */
    public function indexForUser(int $userId): array
    {
        $globalFeatures = FeatureSetting::orderBy('group_name')
            ->orderBy('sort_order')
            ->get();

        $userSettings = UserFeatureSetting::where('user_id', $userId)
            ->pluck('is_enabled', 'feature_key')
            ->toArray();

        return $globalFeatures->map(fn ($f) => [
            'key' => $f->key,
            'label' => $f->label,
            'description' => $f->description,
            'group_name' => $f->group_name,
            'sort_order' => $f->sort_order,
            'is_enabled' => array_key_exists($f->key, $userSettings)
                ? (int) $userSettings[$f->key]
                : (int) $f->is_enabled,
            'is_custom' => array_key_exists($f->key, $userSettings),
        ])->toArray();
    }

    /**
     * Ambil hanya key fitur yang aktif untuk user (untuk sidebar filtering).
     */
    public function enabledKeysForUser(int $userId): array
    {
        $globalFeatures = FeatureSetting::where('is_enabled', 1)->pluck('key')->toArray();
        $userSettings = UserFeatureSetting::where('user_id', $userId)
            ->pluck('is_enabled', 'feature_key')
            ->toArray();

        $result = [];
        foreach ($globalFeatures as $key) {
            $val = $userSettings[$key] ?? 1;
            if ($val) {
                $result[] = $key;
            }
        }
        // Also include user-customized features that are enabled but globally disabled
        foreach ($userSettings as $key => $val) {
            if ($val && ! in_array($key, $result)) {
                $result[] = $key;
            }
        }

        return $result;
    }

    /**
     * Sync fitur per-user sekaligus (batch upsert).
     *
     * @param  array<int, array{key: string, is_enabled: int}>  $items
     */
    public function syncForUser(int $userId, array $items): array
    {
        foreach ($items as $item) {
            UserFeatureSetting::updateOrCreate(
                ['user_id' => $userId, 'feature_key' => $item['key']],
                ['is_enabled' => $item['is_enabled'] ? 1 : 0],
            );
        }

        return $this->indexForUser($userId);
    }
}
