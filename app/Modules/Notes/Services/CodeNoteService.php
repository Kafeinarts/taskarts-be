<?php

declare(strict_types=1);

namespace App\Modules\Notes\Services;

use App\Modules\Notes\Models\CodeNote;
use App\Services\BaseService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

/**
 * CodeNoteService
 *
 * Service modul Notes untuk CRUD catatan kode / code snippets.
 */
class CodeNoteService extends BaseService
{
    /**
     * Daftar catatan kode milik user dengan filter opsional.
     *
     * @param  array<string,mixed>  $filters  q, language, favorite, pinned
     */
    public function index(array $filters = []): Collection
    {
        return $this->own(CodeNote::query())
            ->when($filters['q'] ?? null, fn (Builder $query, string $q) => $query->where('title', 'like', "%{$q}%"))
            ->when($filters['language'] ?? null, fn (Builder $query, string $language) => $query->where('language', $language))
            ->when(\array_key_exists('favorite', $filters) && $filters['favorite'] !== null, fn (Builder $query) => $query->where('is_favorite', normalize_boolean($filters['favorite'])))
            ->when(\array_key_exists('pinned', $filters) && $filters['pinned'] !== null, fn (Builder $query) => $query->where('is_pinned', normalize_boolean($filters['pinned'])))
            ->orderByDesc('is_pinned')
            ->latest()
            ->get();
    }

    /**
     * Ambil satu catatan kode.
     */
    public function show(CodeNote $note): CodeNote
    {
        return $note;
    }

    /**
     * Buat catatan kode baru.
     *
     * @param  array<string,mixed>  $data
     */
    public function store(array $data): CodeNote
    {
        $data['user_id'] = $this->userId();
        $data['tags'] = $this->tags($data['tags'] ?? []);

        return CodeNote::create($data);
    }

    /**
     * Perbarui catatan kode yang sudah ada.
     *
     * @param  array<string,mixed>  $data
     */
    public function update(CodeNote $note, array $data): CodeNote
    {
        if (array_key_exists('tags', $data)) {
            $data['tags'] = $this->tags($data['tags']);
        }

        $note->update($data);

        return $note->fresh();
    }

    /**
     * Hapus catatan kode.
     */
    public function destroy(CodeNote $note): void
    {
        $note->delete();
    }
}
