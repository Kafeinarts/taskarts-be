<?php

declare(strict_types=1);

namespace App\Modules\Planner\Controllers;

use App\Http\Controllers\Api\ApiController;
use App\Modules\Planner\Models\WorkAlarm;
use App\Modules\Planner\Services\WorkAlarmService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * WorkAlarmController
 *
 * Kontroler RESTful untuk resource alarm/pengingat modul Planner.
 */
class WorkAlarmController extends ApiController
{
    /**
     * Daftar alarm, opsional filter status aktif.
     */
    public function index(Request $request, WorkAlarmService $service): JsonResponse
    {
        $enabled = $request->has('enabled') ? normalize_boolean($request->query('enabled')) : null;

        return $this->ok($service->index($enabled), 'Daftar alarm');
    }

    /**
     * Detail satu alarm.
     */
    public function show(WorkAlarm $alarm, WorkAlarmService $service): JsonResponse
    {
        return $this->ok($service->show($alarm), 'Detail alarm');
    }

    /**
     * Buat alarm baru.
     */
    public function store(Request $request, WorkAlarmService $service): JsonResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'time' => ['required', 'date_format:H:i'],
            'days' => ['nullable'],
            'enabled' => ['nullable', 'boolean'],
            'snooze' => ['nullable', 'boolean'],
            'sound' => ['nullable', 'string', 'max:100'],
            'note' => ['nullable', 'string'],
        ]);

        $alarm = $service->store($data);

        return $this->ok($alarm, 'Alarm dibuat', 201);
    }

    /**
     * Perbarui alarm yang sudah ada.
     */
    public function update(Request $request, WorkAlarm $alarm, WorkAlarmService $service): JsonResponse
    {
        $data = $request->validate([
            'title' => ['sometimes', 'string', 'max:255'],
            'time' => ['sometimes', 'date_format:H:i'],
            'days' => ['nullable'],
            'enabled' => ['nullable', 'boolean'],
            'snooze' => ['nullable', 'boolean'],
            'sound' => ['nullable', 'string', 'max:100'],
            'note' => ['nullable', 'string'],
        ]);

        return $this->ok($service->update($alarm, $data), 'Alarm diperbarui');
    }

    /**
     * Hapus alarm.
     */
    public function destroy(WorkAlarm $alarm, WorkAlarmService $service): JsonResponse
    {
        $service->destroy($alarm);

        return $this->ok([], 'Alarm dihapus');
    }
}
