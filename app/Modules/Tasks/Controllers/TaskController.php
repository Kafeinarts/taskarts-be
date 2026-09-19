<?php

declare(strict_types=1);

namespace App\Modules\Tasks\Controllers;

use App\Http\Controllers\Api\ApiController;
use App\Modules\Tasks\Models\Task;
use App\Modules\Tasks\Services\TaskService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * TaskController
 *
 * Kontroler RESTful untuk resource Task pada modul Tasks.
 */
class TaskController extends ApiController
{
    /**
     * Daftar tugas milik user dengan filter opsional.
     */
    public function index(Request $request, TaskService $service): JsonResponse
    {
        $tasks = $service->index($request->query());

        return $this->ok($tasks, 'Daftar tugas');
    }

    /**
     * Detail satu tugas.
     */
    public function show(Task $task, TaskService $service): JsonResponse
    {
        return $this->ok($service->show($task), 'Detail tugas');
    }

    /**
     * Buat tugas baru.
     */
    public function store(Request $request, TaskService $service): JsonResponse
    {
        $data = $request->validate([
            'project_id' => ['nullable', 'integer', 'exists:projects,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'due_date' => ['nullable', 'date'],
            'status' => ['nullable', 'string', 'in:todo,in_progress,in_review,done,archived'],
            'priority' => ['nullable', 'string', 'in:low,medium,high,urgent'],
            'category' => ['nullable', 'string', 'max:255'],
            'tags' => ['nullable'],
            'is_completed' => ['nullable', 'boolean'],
            'time_spent_minutes' => ['nullable', 'integer', 'min:0'],
        ]);

        $task = $service->store($data);

        return $this->ok($task, 'Tugas dibuat', 201);
    }

    /**
     * Perbarui tugas yang sudah ada.
     */
    public function update(Request $request, Task $task, TaskService $service): JsonResponse
    {
        $data = $request->validate([
            'project_id' => ['nullable', 'integer', 'exists:projects,id'],
            'title' => ['sometimes', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'due_date' => ['nullable', 'date'],
            'status' => ['nullable', 'string', 'in:todo,in_progress,in_review,done,archived'],
            'priority' => ['nullable', 'string', 'in:low,medium,high,urgent'],
            'category' => ['nullable', 'string', 'max:255'],
            'tags' => ['nullable'],
            'is_completed' => ['nullable', 'boolean'],
            'time_spent_minutes' => ['nullable', 'integer', 'min:0'],
        ]);

        $updated = $service->update($task, $data);

        return $this->ok($updated, 'Tugas diperbarui');
    }

    /**
     * Hapus tugas.
     */
    public function destroy(Task $task, TaskService $service): JsonResponse
    {
        $service->destroy($task);

        return $this->ok([], 'Tugas dihapus');
    }

    /**
     * Statistik tugas (total, selesai, overdue, dsb).
     */
    public function stats(TaskService $service): JsonResponse
    {
        return $this->ok($service->stats(), 'Statistik tugas');
    }

    /**
     * Tandai tugas selesai.
     */
    public function complete(Task $task, TaskService $service): JsonResponse
    {
        return $this->ok($service->complete($task), 'Tugas ditandai selesai');
    }

    /**
     * Buka kembali tugas yang sudah selesai.
     */
    public function reopen(Task $task, TaskService $service): JsonResponse
    {
        return $this->ok($service->reopen($task), 'Tugas dibuka kembali');
    }
}
