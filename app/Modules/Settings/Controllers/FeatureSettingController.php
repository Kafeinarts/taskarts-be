<?php

declare(strict_types=1);

namespace App\Modules\Settings\Controllers;

use App\Http\Controllers\Api\ApiController;
use App\Models\User;
use App\Modules\Settings\Models\FeatureSetting;
use App\Modules\Settings\Services\FeatureSettingService;
use App\Modules\Settings\Services\UserFeatureSettingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * FeatureSettingController
 *
 * is_enabled: 1 = aktif, 0 = nonaktif.
 * Index & enabled bisa diakses semua user (render sidebar).
 * Toggle & sync hanya admin.
 * User-specific endpoints untuk per-user feature gate.
 */
class FeatureSettingController extends ApiController
{
    /**
     * Daftar seluruh fitur beserta status global (admin).
     */
    public function index(FeatureSettingService $service): JsonResponse
    {
        return $this->ok($service->index(), 'Daftar fitur');
    }

    /**
     * Daftar fitur yang aktif saja (user biasa — global).
     */
    public function enabled(FeatureSettingService $service): JsonResponse
    {
        return $this->ok($service->enabled(), 'Fitur aktif');
    }

    /**
     * Toggle satu fitur (admin).
     */
    public function toggle(Request $request, FeatureSetting $feature, FeatureSettingService $service): JsonResponse
    {
        $this->ensureAdmin($request);

        return $this->ok($service->toggle($feature), 'Status fitur diperbarui');
    }

    /**
     * Sync status seluruh fitur sekaligus (admin — global).
     */
    public function sync(Request $request, FeatureSettingService $service): JsonResponse
    {
        $this->ensureAdmin($request);

        $data = $request->validate([
            'features' => ['required', 'array'],
            'features.*.key' => ['required', 'string', 'exists:feature_settings,key'],
            'features.*.is_enabled' => ['required', 'integer', 'in:0,1'],
        ]);

        return $this->ok($service->sync($data['features']), 'Fitur diperbarui');
    }

    /**
     * Daftar fitur per-user (admin).
     */
    public function userFeatures(Request $request, User $user, UserFeatureSettingService $service): JsonResponse
    {
        $this->ensureAdmin($request);

        return $this->ok($service->indexForUser($user->id), 'Fitur user');
    }

    /**
     * Fitur yang aktif untuk user tertentu (untuk sidebar filtering).
     */
    public function userEnabled(Request $request, User $user, UserFeatureSettingService $service): JsonResponse
    {
        // User bisa melihat fitur sendiri; admin bisa melihat semua
        if (! $request->user()->isAdmin() && $request->user()->id !== $user->id) {
            abort(403);
        }

        return $this->ok($service->enabledKeysForUser($user->id), 'Fitur aktif user');
    }

    /**
     * Sync fitur per-user (admin).
     */
    public function syncUserFeatures(Request $request, User $user, UserFeatureSettingService $service): JsonResponse
    {
        $this->ensureAdmin($request);

        $data = $request->validate([
            'features' => ['required', 'array'],
            'features.*.key' => ['required', 'string', 'exists:feature_settings,key'],
            'features.*.is_enabled' => ['required', 'integer', 'in:0,1'],
        ]);

        return $this->ok($service->syncForUser($user->id, $data['features']), 'Fitur user diperbarui');
    }

    /**
     * Pastikan user adalah admin.
     */
    private function ensureAdmin(Request $request): void
    {
        if (! $request->user()->isAdmin()) {
            abort(403, 'Hanya admin yang dapat mengubah pengaturan fitur.');
        }
    }
}
