<?php

declare(strict_types=1);

namespace App\Modules\Planner\Services;

use App\Modules\Planner\Models\MoodLog;
use App\Services\BaseService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

/**
 * MoodLogService
 *
 * Service modul Planner untuk pencatatan & rekapitulasi suasana hati (mood).
 */
class MoodLogService extends BaseService
{
    /**
     * Daftar log mood milik user, opsional filter by date range.
     *
     * @param  array<string,mixed>  $filters  from, to
     */
    public function index(array $filters = []): Collection
    {
        return $this->own(MoodLog::query())
            ->when($filters['from'] ?? null, fn (Builder $query, string $from) => $query->where('logged_at', '>=', $from.' 00:00:00'))
            ->when($filters['to'] ?? null, fn (Builder $query, string $to) => $query->where('logged_at', '<=', $to.' 23:59:59'))
            ->orderByDesc('logged_at')
            ->get();
    }

    /**
     * Buat log mood baru.
     *
     * @param  array<string,mixed>  $data
     */
    public function store(array $data): MoodLog
    {
        $data['user_id'] = $this->userId();
        $data['logged_at'] = $data['logged_at'] ?? now();

        return MoodLog::create($data);
    }

    /**
     * Hapus log mood.
     */
    public function destroy(MoodLog $log): void
    {
        $log->delete();
    }

    /**
     * Rekapitulasi mood 7 hari terakhir.
     *
     * @return array<string,mixed>
     */
    public function summary(): array
    {
        $logs = $this->own(MoodLog::query())
            ->where('logged_at', '>=', now()->subDays(6)->startOfDay())
            ->orderByDesc('logged_at')
            ->get();

        return [
            'average_score' => round((float) $logs->avg('score'), 2),
            'total' => $logs->count(),
            'last_mood' => $logs->first(),
            'trend' => $logs->map(fn (MoodLog $log) => [
                'date' => $log->logged_at?->toDateString(),
                'score' => $log->score,
                'mood_label' => $log->mood_label,
            ])->values(),
        ];
    }
}
