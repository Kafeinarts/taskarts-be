<?php

declare(strict_types=1);

namespace App\Modules\Tasks\Services;

use App\Modules\Tasks\Models\Task;
use App\Services\BaseService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

/**
 * TaskService
 *
 * Service modul Tasks untuk seluruh operasi CRUD tugas/to-do list,
 * termasuk statistik dan toggle status.
 */
class TaskService extends BaseService
{
    /**
     * Daftar tugas milik user dengan filter opsional.
     *
     * @param  array<string,mixed>  $filters  status, priority, project_id, category, q, done
     */
    public function index(array $filters = []): Collection
    {
        return $this->own(Task::query())
            ->with('project:id,name,color')
            ->when($filters['q'] ?? null, fn (Builder $query, string $q) => $query->where('title', 'like', "%{$q}%"))
            ->when($filters['status'] ?? null, fn (Builder $query, string $status) => $query->where('status', $status))
            ->when($filters['priority'] ?? null, fn (Builder $query, string $priority) => $query->where('priority', $priority))
            ->when($filters['category'] ?? null, fn (Builder $query, string $category) => $query->where('category', $category))
            ->when($filters['project_id'] ?? null, fn (Builder $query, int $projectId) => $query->where('project_id', $projectId))
            ->when(\array_key_exists('done', $filters) && $filters['done'] !== null, fn (Builder $query) => $query->where('is_completed', normalize_boolean($filters['done'])))
            ->orderBy('is_completed')
            ->orderByDesc('due_date')
            ->orderByDesc('priority')
            ->latest()
            ->get();
    }

    /**
     * Ambil satu tugas.
     */
    public function show(Task $task): Task
    {
        $task->load('project:id,name,color');

        return $task;
    }

    /**
     * Buat tugas baru.
     *
     * @param  array<string,mixed>  $data
     */
    public function store(array $data): Task
    {
        $data['user_id'] = $this->userId();
        $data['tags'] = $this->tags($data['tags'] ?? []);

        $task = Task::create($data);

        if (normalize_boolean($data['is_completed'] ?? false)) {
            $this->complete($task);
        }

        return $task->load('project:id,name,color');
    }

    /**
     * Perbarui tugas yang sudah ada.
     *
     * @param  array<string,mixed>  $data
     */
    public function update(Task $task, array $data): Task
    {
        if (array_key_exists('tags', $data)) {
            $data['tags'] = $this->tags($data['tags']);
        }

        if (array_key_exists('is_completed', $data)) {
            normalize_boolean($data['is_completed']) ? $this->complete($task) : $this->reopen($task);
            unset($data['is_completed']);
        }

        $task->update($data);

        return $task->load('project:id,name,color');
    }

    /**
     * Hapus tugas.
     */
    public function destroy(Task $task): void
    {
        $task->delete();
    }

    /**
     * Tandai tugas selesai: set status done, is_completed true, dan completed_at.
     */
    public function complete(Task $task): Task
    {
        $task->update([
            'is_completed' => true,
            'status' => 'done',
            'completed_at' => now(),
        ]);

        return $task;
    }

    /**
     * Buka kembali tugas yang sudah selesai.
     */
    public function reopen(Task $task): Task
    {
        $task->update([
            'is_completed' => false,
            'status' => 'todo',
            'completed_at' => null,
        ]);

        return $task;
    }

    /**
     * Rekapitulasi / statistik tugas milik user.
     *
     * @return array<string,mixed>
     */
    public function stats(): array
    {
        $base = $this->own(Task::query());

        return [
            'total' => (clone $base)->count(),
            'completed' => (clone $base)->where('is_completed', true)->count(),
            'in_progress' => (clone $base)->where('status', 'in_progress')->count(),
            'todo' => (clone $base)->where('status', 'todo')->count(),
            'overdue' => (clone $base)->where('is_completed', false)->where('due_date', '<', now()->toDateString())->count(),
            'due_today' => (clone $base)->where('is_completed', false)->where('due_date', now()->toDateString())->count(),
            'completion_rate' => $this->completionRate(),
        ];
    }

    /**
     * Persentase penyelesaian tugas (0-100).
     */
    public function completionRate(): float
    {
        $total = (int) $this->own(Task::query())->count();

        if ($total === 0) {
            return 0.0;
        }

        $done = (int) $this->own(Task::query())->where('is_completed', true)->count();

        return round(($done / $total) * 100, 2);
    }
}
