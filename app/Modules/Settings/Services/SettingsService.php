<?php

declare(strict_types=1);

namespace App\Modules\Settings\Services;

use App\Modules\Settings\Models\Setting;
use App\Services\BaseService;
use Illuminate\Support\Collection;

/**
 * SettingsService
 *
 * Service modul Settings untuk penyimpanan pengaturan key-value yang modular.
 * Dipakai untuk user profile, business profile, dan preferensi aplikasi.
 */
class SettingsService extends BaseService
{
    /**
     * Ambil nilai satu pengaturan dengan fallback bila tidak ditemukan.
     */
    public function get(string $key, mixed $default = null): mixed
    {
        $setting = Setting::where('key', $key)->first();

        return $setting?->value_parsed ?? $default;
    }

    /**
     * Simpan (upsert) satu pengaturan berdasarkan key.
     */
    public function set(string $key, mixed $value, ?string $group = null): Setting
    {
        $setting = Setting::firstOrNew(['key' => $key]);
        $setting->setValueData($value);

        if ($group !== null) {
            $setting->group = $group;
        }

        $setting->save();

        return $setting;
    }

    /**
     * Semua pengaturan milik grup tertentu (opsional).
     */
    public function all(?string $group = null): Collection
    {
        $query = Setting::query();

        if ($group !== null) {
            $query->where('group', $group);
        }

        return $query->orderBy('key')->get();
    }

    /**
     * Ambil pengaturan satu grup sebagai pasangan key => value terparsing.
     *
     * @return array<string,mixed>
     */
    public function group(string $group): array
    {
        return $this->all($group)
            ->mapWithKeys(fn (Setting $setting) => [$setting->key => $setting->value_parsed])
            ->all();
    }

    /**
     * Simpan banyak pengaturan sekaligus dalam satu grup.
     *
     * @param  array<string,mixed>  $pairs
     */
    public function setBatch(string $group, array $pairs): void
    {
        foreach ($pairs as $key => $value) {
            $this->set($key, $value, $group);
        }
    }

    /**
     * Hapus satu pengaturan berdasarkan key.
     */
    public function forget(string $key): void
    {
        Setting::where('key', $key)->delete();
    }
}
