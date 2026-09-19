<?php

declare(strict_types=1);

namespace App\Modules\Habits\Controllers;

use App\Http\Controllers\Api\ApiController;
use App\Modules\Habits\Models\Habit;
use App\Modules\Habits\Services\HabitService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * HabitController
 *
 * Kontroler RESTful untuk resource kebiasaan (habit) modul Habits,
 * termasuk pencatatan log harian dan perhitungan streak.
 */
class HabitController extends ApiController
{
    /**
     * Daftar kebiasaan, opsional filter status aktif.
     */
    public function index(Request $request, HabitService $service): JsonResponse
    {
        $active = $request->has('active') ? normalize_boolean($request->query('active')) : null;

        return $this->ok($service->index($active), 'Daftar kebiasaan');
    }

    /**
     * Detail satu kebiasaan beserta log 30 hari terakhir & streak.
     */
    public function show(Habit $habit, HabitService $service): JsonResponse
    {
        return $this->ok($service->show($habit), 'Detail kebiasaan');
    }

    /**
     * Buat kebiasaan baru.
     */
    public function store(Request $request, HabitService $service): JsonResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'frequency' => ['nullable', 'string', 'in:daily,weekly,monthly'],
            'category' => ['nullable', 'string', 'max:255'],
            'color' => ['nullable', 'string', 'max:20'],
            'icon' => ['nullable', 'string', 'max:100'],
            'is_active' => ['nullable', 'boolean'],
            'target' => ['nullable', 'numeric', 'min:0'],
            'goal_type' => ['nullable', 'string', 'in:habit,limit,count'],
        ]);

        $habit = $service->store($data);

        return $this->ok($habit, 'Kebiasaan dibuat', 201);
    }

    /**
     * Perbarui kebiasaan yang sudah ada.
     */
    public function update(Request $request, Habit $habit, HabitService $service): JsonResponse
    {
        $data = $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'frequency' => ['nullable', 'string', 'in:daily,weekly,monthly'],
            'category' => ['nullable', 'string', 'max:255'],
            'color' => ['nullable', 'string', 'max:20'],
            'icon' => ['nullable', 'string', 'max:100'],
            'is_active' => ['nullable', 'boolean'],
            'target' => ['nullable', 'numeric', 'min:0'],
            'goal_type' => ['nullable', 'string', 'in:habit,limit,count'],
        ]);

        $updated = $service->update($habit, $data);

        return $this->ok($updated, 'Kebiasaan diperbarui');
    }

    /**
     * Hapus kebiasaan beserta log-nya.
     */
    public function destroy(Habit $habit, HabitService $service): JsonResponse
    {
        $service->destroy($habit);

        return $this->ok([], 'Kebiasaan dihapus');
    }

    /**
     * Catat pencapaian kebiasaan pada tanggal tertentu.
     */
    public function log(Request $request, Habit $habit, HabitService $service): JsonResponse
    {
        $data = $request->validate([
            'log_date' => ['nullable', 'date'],
            'value' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string'],
        ]);

        $log = $service->log($habit, $data);

        return $this->ok($log, 'Pencapaian dicatat', 201);
    }

    /**
     * Hapus log kebiasaan pada tanggal tertentu.
     */
    public function unlog(Request $request, Habit $habit, HabitService $service): JsonResponse
    {
        $data = $request->validate([
            'log_date' => ['required', 'date'],
        ]);

        $service->unlog($habit, $data['log_date']);

        return $this->ok([], 'Log kebiasaan dihapus');
    }
}
