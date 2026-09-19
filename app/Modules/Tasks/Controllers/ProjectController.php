<?php

declare(strict_types=1);

namespace App\Modules\Tasks\Controllers;

use App\Http\Controllers\Api\ApiController;
use App\Modules\Tasks\Models\Project;
use App\Modules\Tasks\Services\ProjectService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * ProjectController
 *
 * Kontroler RESTful untuk resource Project pada modul Tasks.
 */
class ProjectController extends ApiController
{
    /**
     * Daftar proyek milik user dengan filter opsional (q, status, priority, archived).
     */
    public function index(Request $request, ProjectService $service): JsonResponse
    {
        $projects = $service->index($request->query());

        return $this->ok($projects, 'Daftar proyek');
    }

    /**
     * Detail satu proyek beserta daftar tugas di dalamnya.
     */
    public function show(Project $project, ProjectService $service): JsonResponse
    {
        return $this->ok($service->show($project), 'Detail proyek');
    }

    /**
     * Buat proyek baru.
     */
    public function store(Request $request, ProjectService $service): JsonResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'start_date' => ['nullable', 'date'],
            'due_date' => ['nullable', 'date'],
            'status' => ['nullable', 'string', 'in:draft,active,on_hold,completed,archived'],
            'priority' => ['nullable', 'string', 'in:low,medium,high,urgent'],
            'color' => ['nullable', 'string', 'max:20'],
            'budget' => ['nullable', 'numeric', 'min:0'],
            'progress' => ['nullable', 'integer', 'min:0', 'max:100'],
            'tags' => ['nullable'],
            'is_archived' => ['nullable', 'boolean'],
        ]);

        $project = $service->store($data);

        return $this->ok($project, 'Proyek dibuat', 201);
    }

    /**
     * Perbarui proyek yang sudah ada.
     */
    public function update(Request $request, Project $project, ProjectService $service): JsonResponse
    {
        $data = $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'start_date' => ['nullable', 'date'],
            'due_date' => ['nullable', 'date'],
            'status' => ['nullable', 'string', 'in:draft,active,on_hold,completed,archived'],
            'priority' => ['nullable', 'string', 'in:low,medium,high,urgent'],
            'color' => ['nullable', 'string', 'max:20'],
            'budget' => ['nullable', 'numeric', 'min:0'],
            'progress' => ['nullable', 'integer', 'min:0', 'max:100'],
            'tags' => ['nullable'],
            'is_archived' => ['nullable', 'boolean'],
        ]);

        $updated = $service->update($project, $data);

        return $this->ok($updated, 'Proyek diperbarui');
    }

    /**
     * Hapus proyek beserta tugas turunannya.
     */
    public function destroy(Project $project, ProjectService $service): JsonResponse
    {
        $service->destroy($project);

        return $this->ok([], 'Proyek dihapus');
    }

    /**
     * Statistik proyek (total, status, budget).
     */
    public function stats(ProjectService $service): JsonResponse
    {
        return $this->ok($service->stats(), 'Statistik proyek');
    }
}
