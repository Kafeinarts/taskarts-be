<?php

declare(strict_types=1);

namespace App\Modules\Habits\Services;

use App\Modules\Habits\Models\Habit;
use App\Modules\Habits\Models\HabitLog;
use App\Services\BaseService;
use Carbon\Carbon;
use Illuminate\Support\Collection;

/**
 * HabitService
 *
 * Service modul Habits untuk CRUD kebiasaan, pencatatan log harian,
 * serta perhitungan streak.
 */
class HabitService extends BaseService
{
    /**
     * Daftar kebiasaan milik user, opsional filter aktif.
     */
    public function index(?bool $active = null): Collection
    {
        $query = $this->own(Habit::query());

        if ($active !== null) {
            $query->where('is_active', $active);
        }

        return $query->withCount('logs')->orderBy('name')->get();
    }

    /**
     * Ambil satu kebiasaan beserta log 30 hari terakhir + streak.
     */
    public function show(Habit $habit): Habit
    {
        $habit->load(['logs' => fn ($query) => $query->where('log_date', '>=', now()->subDays(30)->toDateString())->orderBy('log_date')]);

        return $habit;
    }

    /**
     * Buat kebiasaan baru.
     *
     * @param  array<string,mixed>  $data
     */
    public function store(array $data): Habit
    {
        $data['user_id'] = $this->userId();

        return Habit::create($data);
    }

    /**
     * Perbarui kebiasaan yang sudah ada.
     *
     * @param  array<string,mixed>  $data
     */
    public function update(Habit $habit, array $data): Habit
    {
        $habit->update($data);

        return $habit->fresh();
    }

    /**
     * Hapus kebiasaan beserta seluruh log-nya.
     */
    public function destroy(Habit $habit): void
    {
        $habit->logs()->delete();
        $habit->delete();
    }

    /**
     * Catat pencapaian kebiasaan pada tanggal tertentu (upsert unik per tanggal).
     *
     * @param  array<string,mixed>  $data
     */
    public function log(Habit $habit, array $data): HabitLog
    {
        $logDate = $data['log_date'] ?? now()->toDateString();

        $log = HabitLog::updateOrCreate(
            ['habit_id' => $habit->id, 'log_date' => $logDate],
            [
                'value' => money_value($data['value'] ?? 1),
                'notes' => $data['notes'] ?? null,
            ],
        );

        $this->recalculateStreak($habit);

        return $log;
    }

    /**
     * Hapus log kebiasaan pada tanggal tertentu.
     */
    public function unlog(Habit $habit, string $logDate): void
    {
        HabitLog::where('habit_id', $habit->id)->where('log_date', $logDate)->delete();
        $this->recalculateStreak($habit);
    }

    /**
     * Hitung ulang streak kontekutif (kebiasaan daily) berdasarkan log terakhir.
     */
    public function recalculateStreak(Habit $habit): int
    {
        if ($habit->frequency !== 'daily') {
            return (int) $habit->streak;
        }

        $dates = $habit->logs()
            ->pluck('log_date')
            ->map(fn (string $date): Carbon => Carbon::parse($date))
            ->sortDesc()
            ->values();
        $streak = 0;
        $cursor = Carbon::now()->startOfDay();

        foreach ($dates as $date) {
            if ($date->equalTo($cursor)) {
                $streak++;
                $cursor = $cursor->subDay();
            } else {
                break;
            }
        }

        $habit->update(['streak' => $streak]);

        return $streak;
    }
}
