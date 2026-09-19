<?php

declare(strict_types=1);

namespace App\Modules\Tasks\Services;

use App\Modules\Tasks\Models\Project;
use App\Services\BaseService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

/**
 * ProjectService
 *
 * Service modul Tasks untuk seluruh operasi CRUD serta statistik proyek.
 */
class ProjectService extends BaseService
{
    /**
     * Daftar proyek milik user dengan filter opsional.
     *
     * @param  array<string,mixed>  $filters  stat, priority, q, archived
     */
    public function index(array $filters = []): Collection
    {
        return $this->own(Project::query())
            ->withCount('tasks')
            ->when($filters['q'] ?? null, fn (Builder $query, string $q) => $query->where('name', 'like', "%{$q}%"))
            ->when($filters['status'] ?? null, fn (Builder $query, string $status) => $query->where('status', $status))
            ->when($filters['priority'] ?? null, fn (Builder $query, string $priority) => $query->where('priority', $priority))
            ->when($filters['archived'] ?? null, fn (Builder $query, bool $archived) => $query->where('is_archived', $archived))
            ->orderByDesc('updated_at')
            ->get();
    }

    /**
     * Ambil satu proyek beserta daftar tugas di dalamnya.
     */
    public function show(Project $project): Project
    {
        $project->load(['tasks' => fn ($query) => $query->orderBy('is_completed')->orderBy('due_date')]);

        return $project;
    }

    /**
     * Buat proyek baru.
     *
     * @param  array<string,mixed>  $data
     */
    public function store(array $data): Project
    {
        $data['user_id'] = $this->userId();
        $data['tags'] = $this->tags($data['tags'] ?? []);
        $data['progress'] = (int) ($data['progress'] ?? 0);

        return Project::create($data);
    }

    /**
     * Perbarui proyek yang sudah ada.
     *
     * @param  array<string,mixed>  $data
     */
    public function update(Project $project, array $data): Project
    {
        if (array_key_exists('tags', $data)) {
            $data['tags'] = $this->tags($data['tags']);
        }

        $project->update($data);

        return $project->fresh();
    }

    /**
     * Hapus proyek (tugas turunan ikut terhapus via FK cascade).
     */
    public function destroy(Project $project): void
    {
        $project->tasks()->delete();
        $project->delete();
    }

    /**
     * Rekapitulasi jumlah proyek berdasarkan status.
     *
     * @return array<string,int>
     */
    public function stats(): array
    {
        $base = $this->own(Project::query());

        return [
            'total' => (clone $base)->count(),
            'active' => (clone $base)->where('status', 'active')->count(),
            'on_hold' => (clone $base)->where('status', 'on_hold')->count(),
            'completed' => (clone $base)->where('status', 'completed')->count(),
            'archived' => (clone $base)->where('is_archived', true)->count(),
            'total_budget' => money_value((clone $base)->sum('budget')),
        ];
    }

    /**
     * Hitung progres rata-rata seluruh proyek aktif (0-100).
     */
    public function averageProgress(): float
    {
        return round((float) $this->own(Project::query())->where('status', '!=', 'archived')->avg('progress'), 2);
    }
}
