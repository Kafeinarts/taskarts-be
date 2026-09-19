<?php

declare(strict_types=1);

namespace App\Modules\Planner\Controllers;

use App\Http\Controllers\Api\ApiController;
use App\Modules\Planner\Models\Event;
use App\Modules\Planner\Services\EventService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * EventController
 *
 * Kontroler RESTful untuk resource agenda/kalender (events) modul Planner.
 */
class EventController extends ApiController
{
    /**
     * Daftar event pada rentang tanggal (default: bulan berjalan).
     */
    public function index(Request $request, EventService $service): JsonResponse
    {
        $filters = $request->query();
        $filters['from'] ??= now()->startOfMonth()->toDateString();
        $filters['to'] ??= now()->endOfMonth()->toDateString();

        return $this->ok($service->index($filters), 'Daftar event');
    }

    /**
     * Detail satu event.
     */
    public function show(Event $event, EventService $service): JsonResponse
    {
        return $this->ok($service->show($event), 'Detail event');
    }

    /**
     * Buat event baru.
     */
    public function store(Request $request, EventService $service): JsonResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'start_at' => ['required', 'date'],
            'end_at' => ['nullable', 'date', 'after:start_at'],
            'all_day' => ['nullable', 'boolean'],
            'calendar' => ['nullable', 'string', 'max:100'],
            'color' => ['nullable', 'string', 'max:20'],
            'location' => ['nullable', 'string', 'max:255'],
        ]);

        $event = $service->store($data);

        return $this->ok($event, 'Event dibuat', 201);
    }

    /**
     * Perbarui event yang sudah ada.
     */
    public function update(Request $request, Event $event, EventService $service): JsonResponse
    {
        $data = $request->validate([
            'title' => ['sometimes', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'start_at' => ['sometimes', 'date'],
            'end_at' => ['nullable', 'date', 'after:start_at'],
            'all_day' => ['nullable', 'boolean'],
            'calendar' => ['nullable', 'string', 'max:100'],
            'color' => ['nullable', 'string', 'max:20'],
            'location' => ['nullable', 'string', 'max:255'],
        ]);

        return $this->ok($service->update($event, $data), 'Event diperbarui');
    }

    /**
     * Hapus event.
     */
    public function destroy(Event $event, EventService $service): JsonResponse
    {
        $service->destroy($event);

        return $this->ok([], 'Event dihapus');
    }

    /**
     * Daftar event terdekat (berikutnya) untuk notifikasi.
     */
    public function upcoming(Request $request, EventService $service): JsonResponse
    {
        $filters = [
            'from' => now()->toDateString(),
            'to' => now()->addDays((int) $request->query('days', 7))->toDateString(),
        ];

        return $this->ok($service->index($filters), 'Event terdekat');
    }
}
