<?php

declare(strict_types=1);

namespace App\Modules\Planner\Controllers;

use App\Http\Controllers\Api\ApiController;
use App\Modules\Planner\Models\MoodLog;
use App\Modules\Planner\Services\MoodLogService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * MoodLogController
 *
 * Kontroler RESTful untuk resource log suasana hati (mood) modul Planner.
 */
class MoodLogController extends ApiController
{
    /**
     * Daftar log mood dengan filter tanggal (from/to).
     */
    public function index(Request $request, MoodLogService $service): JsonResponse
    {
        return $this->ok($service->index($request->query()), 'Daftar log mood');
    }

    /**
     * Buat log mood baru.
     */
    public function store(Request $request, MoodLogService $service): JsonResponse
    {
        $data = $request->validate([
            'mood' => ['nullable', 'string', 'max:50'],
            'mood_label' => ['nullable', 'string', 'max:100'],
            'score' => ['nullable', 'integer', 'min:1', 'max:10'],
            'note' => ['nullable', 'string'],
            'logged_at' => ['nullable', 'date'],
        ]);

        $log = $service->store($data);

        return $this->ok($log, 'Log mood dibuat', 201);
    }

    /**
     * Hapus log mood.
     */
    public function destroy(MoodLog $log, MoodLogService $service): JsonResponse
    {
        $service->destroy($log);

        return $this->ok([], 'Log mood dihapus');
    }

    /**
     * Rekapitulasi mood 7 hari terakhir.
     */
    public function summary(MoodLogService $service): JsonResponse
    {
        return $this->ok($service->summary(), 'Ringkasan mood');
    }
}
