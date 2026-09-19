<?php

declare(strict_types=1);

namespace App\Modules\Planner\Services;

use App\Modules\Planner\Models\Event;
use App\Services\BaseService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

/**
 * EventService
 *
 * Service modul Planner untuk CRUD agenda/kalender (events).
 */
class EventService extends BaseService
{
    /**
     * Daftar event milik user dalam rentang tanggal pilihan.
     *
     * @param  array<string,mixed>  $filters  from, to, calendar
     */
    public function index(array $filters = []): Collection
    {
        return $this->own(Event::query())
            ->when($filters['from'] ?? null, fn (Builder $query, string $from) => $query->whereDate('start_at', '>=', $from))
            ->when($filters['to'] ?? null, fn (Builder $query, string $to) => $query->whereDate('start_at', '<=', $to))
            ->when($filters['calendar'] ?? null, fn (Builder $query, string $calendar) => $query->where('calendar', $calendar))
            ->orderBy('start_at')
            ->get();
    }

    /**
     * Ambil satu event.
     */
    public function show(Event $event): Event
    {
        return $event;
    }

    /**
     * Buat event baru.
     *
     * @param  array<string,mixed>  $data
     */
    public function store(array $data): Event
    {
        $data['user_id'] = $this->userId();

        return Event::create($data);
    }

    /**
     * Perbarui event yang sudah ada.
     *
     * @param  array<string,mixed>  $data
     */
    public function update(Event $event, array $data): Event
    {
        $event->update($data);

        return $event->fresh();
    }

    /**
     * Hapus event.
     */
    public function destroy(Event $event): void
    {
        $event->delete();
    }
}
