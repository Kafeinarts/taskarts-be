<?php

declare(strict_types=1);

namespace App\Modules\Planner\Services;

use App\Modules\Planner\Models\WorkAlarm;
use App\Services\BaseService;
use Illuminate\Support\Collection;

/**
 * WorkAlarmService
 *
 * Service modul Planner untuk CRUD alarm/pengingat waktu kerja.
 */
class WorkAlarmService extends BaseService
{
    /**
     * Daftar alarm milik user, opsional filter status aktif.
     */
    public function index(?bool $enabled = null): Collection
    {
        $query = $this->own(WorkAlarm::query());

        if ($enabled !== null) {
            $query->where('enabled', $enabled);
        }

        return $query->orderBy('time')->get();
    }

    /**
     * Ambil satu alarm.
     */
    public function show(WorkAlarm $alarm): WorkAlarm
    {
        return $alarm;
    }

    /**
     * Buat alarm baru.
     *
     * @param  array<string,mixed>  $data
     */
    public function store(array $data): WorkAlarm
    {
        $data['user_id'] = $this->userId();
        $data['days'] = $this->tags($data['days'] ?? []);

        return WorkAlarm::create($data);
    }

    /**
     * Perbarui alarm yang sudah ada.
     *
     * @param  array<string,mixed>  $data
     */
    public function update(WorkAlarm $alarm, array $data): WorkAlarm
    {
        if (array_key_exists('days', $data)) {
            $data['days'] = $this->tags($data['days']);
        }

        $alarm->update($data);

        return $alarm->fresh();
    }

    /**
     * Hapus alarm.
     */
    public function destroy(WorkAlarm $alarm): void
    {
        $alarm->delete();
    }
}
