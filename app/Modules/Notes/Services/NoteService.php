<?php

declare(strict_types=1);

namespace App\Modules\Notes\Services;

use App\Modules\Notes\Models\Note;
use App\Services\BaseService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

/**
 * NoteService
 *
 * Service modul Notes untuk CRUD catatan umum / sticky notes.
 */
class NoteService extends BaseService
{
    /**
     * Daftar catatan milik user dengan filter opsional.
     *
     * @param  array<string,mixed>  $filters  q, pinned, archived, category
     */
    public function index(array $filters = []): Collection
    {
        return $this->own(Note::query())
            ->when($filters['q'] ?? null, fn (Builder $query, string $q) => $query->where('title', 'like', "%{$q}%")->orWhere('content', 'like', "%{$q}%"))
            ->when($filters['category'] ?? null, fn (Builder $query, string $category) => $query->where('category', $category))
            ->when(\array_key_exists('pinned', $filters) && $filters['pinned'] !== null, fn (Builder $query) => $query->where('is_pinned', normalize_boolean($filters['pinned'])))
            ->when(\array_key_exists('archived', $filters) && $filters['archived'] !== null, fn (Builder $query) => $query->where('is_archived', normalize_boolean($filters['archived'])))
            ->orderByDesc('is_pinned')
            ->latest()
            ->get();
    }

    /**
     * Ambil satu catatan.
     */
    public function show(Note $note): Note
    {
        return $note;
    }

    /**
     * Buat catatan baru.
     *
     * @param  array<string,mixed>  $data
     */
    public function store(array $data): Note
    {
        $data['user_id'] = $this->userId();
        $data['tags'] = $this->tags($data['tags'] ?? []);

        return Note::create($data);
    }

    /**
     * Perbarui catatan yang sudah ada.
     *
     * @param  array<string,mixed>  $data
     */
    public function update(Note $note, array $data): Note
    {
        if (array_key_exists('tags', $data)) {
            $data['tags'] = $this->tags($data['tags']);
        }

        $note->update($data);

        return $note->fresh();
    }

    /**
     * Hapus catatan.
     */
    public function destroy(Note $note): void
    {
        $note->delete();
    }
}
