<?php

declare(strict_types=1);

namespace App\Modules\Notes\Services;

use App\Modules\Notes\Models\DiaryEntry;
use App\Services\BaseService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

/**
 * DiaryService
 *
 * Service modul Notes untuk CRUD jurnal / buku harian.
 */
class DiaryService extends BaseService
{
    /**
     * Daftar entri jurnal milik user dengan filter opsional.
     *
     * @param  array<string,mixed>  $filters  q, mood, from, to
     */
    public function index(array $filters = []): Collection
    {
        return $this->own(DiaryEntry::query())
            ->when($filters['q'] ?? null, fn (Builder $query, string $q) => $query->where('title', 'like', "%{$q}%")->orWhere('content', 'like', "%{$q}%"))
            ->when($filters['mood'] ?? null, fn (Builder $query, string $mood) => $query->where('mood', $mood))
            ->when($filters['from'] ?? null, fn (Builder $query, string $from) => $query->whereDate('entry_date', '>=', $from))
            ->when($filters['to'] ?? null, fn (Builder $query, string $to) => $query->whereDate('entry_date', '<=', $to))
            ->orderByDesc('entry_date')
            ->latest()
            ->get();
    }

    /**
     * Ambil satu entri jurnal.
     */
    public function show(DiaryEntry $entry): DiaryEntry
    {
        return $entry;
    }

    /**
     * Buat entri jurnal baru.
     *
     * @param  array<string,mixed>  $data
     */
    public function store(array $data): DiaryEntry
    {
        $data['user_id'] = $this->userId();
        $data['entry_date'] = $data['entry_date'] ?? now()->toDateString();
        $data['entry_time'] = $data['entry_time'] ?? now()->format('H:i:s');
        $data['tags'] = $this->tags($data['tags'] ?? []);

        return DiaryEntry::create($data);
    }

    /**
     * Perbarui entri jurnal yang sudah ada.
     *
     * @param  array<string,mixed>  $data
     */
    public function update(DiaryEntry $entry, array $data): DiaryEntry
    {
        if (array_key_exists('tags', $data)) {
            $data['tags'] = $this->tags($data['tags']);
        }

        $entry->update($data);

        return $entry->fresh();
    }

    /**
     * Hapus entri jurnal.
     */
    public function destroy(DiaryEntry $entry): void
    {
        $entry->delete();
    }
}
