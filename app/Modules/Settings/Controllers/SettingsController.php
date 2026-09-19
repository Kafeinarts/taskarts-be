<?php

declare(strict_types=1);

namespace App\Modules\Settings\Controllers;

use App\Http\Controllers\Api\ApiController;
use App\Modules\Settings\Services\SettingsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * SettingsController
 *
 * Endpoint pengaturan key-value yang modular (user profile, business profile,
 * preferensi aplikasi, dsb).
 */
class SettingsController extends ApiController
{
    /**
     * Ambil seluruh pengaturan (atau per grup).
     */
    public function index(Request $request, SettingsService $service): JsonResponse
    {
        $details = $service->all($request->query('group'));

        return $this->ok($details, 'Daftar pengaturan');
    }

    /**
     * Ambil pengaturan satu grup sebagai pasangan key => value.
     */
    public function group(string $group, SettingsService $service): JsonResponse
    {
        return $this->ok($service->group($group), "Pengaturan grup {$group}");
    }

    /**
     * Ambil nilai satu pengaturan berdasarkan key.
     */
    public function show(string $key, SettingsService $service): JsonResponse
    {
        return $this->ok(['key' => $key, 'value' => $service->get($key)], "Pengaturan {$key}");
    }

    /**
     * Simpan (upsert) satu pengaturan berdasarkan key.
     */
    public function store(Request $request, string $key, SettingsService $service): JsonResponse
    {
        $data = $request->validate([
            'value' => ['nullable'],
            'group' => ['nullable', 'string', 'max:255'],
        ]);

        $setting = $service->set($key, $data['value'], $data['group'] ?? null);

        return $this->ok($setting, "Pengaturan {$key} disimpan", 201);
    }

    /**
     * Simpan banyak pengaturan sekaligus dalam satu grup (batch).
     */
    public function storeBatch(Request $request, SettingsService $service): JsonResponse
    {
        $data = $request->validate([
            'group' => ['required', 'string', 'max:255'],
            'values' => ['required', 'array'],
        ]);

        $service->setBatch($data['group'], $data['values']);

        return $this->ok(
            $service->group($data['group']),
            "Pengaturan grup {$data['group']} disimpan",
        );
    }

    /**
     * Hapus satu pengaturan berdasarkan key.
     */
    public function destroy(string $key, SettingsService $service): JsonResponse
    {
        $service->forget($key);

        return $this->ok([], "Pengaturan {$key} dihapus");
    }
}
