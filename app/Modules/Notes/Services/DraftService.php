<?php

declare(strict_types=1);

namespace App\Modules\Notes\Services;

use App\Modules\Notes\Models\Draft;
use App\Services\BaseService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

/**
 * DraftService
 *
 * Service modul Notes untuk CRUD draf tulisan (Medium-style drafts).
 */
class DraftService extends BaseService
{
    /**
     * Daftar draf milik user dengan filter opsional.
     *
     * @param  array<string,mixed>  $filters  q, status, type
     */
    public function index(array $filters = []): Collection
    {
        return $this->own(Draft::query())
            ->when($filters['q'] ?? null, fn (Builder $query, string $q) => $query->where('title', 'like', "%{$q}%"))
            ->when($filters['status'] ?? null, fn (Builder $query, string $status) => $query->where('status', $status))
            ->when($filters['type'] ?? null, fn (Builder $query, string $type) => $query->where('type', $type))
            ->orderByDesc('created_at')
            ->get();
    }

    /**
     * Ambil satu draf.
     */
    public function show(Draft $draft): Draft
    {
        return $draft;
    }

    /**
     * Buat draf baru.
     *
     * @param  array<string,mixed>  $data
     */
    public function store(array $data): Draft
    {
        $data['user_id'] = $this->userId();
        $data['tags'] = $this->tags($data['tags'] ?? []);

        return Draft::create($data);
    }

    /**
     * Perbarui draf yang sudah ada.
     *
     * @param  array<string,mixed>  $data
     */
    public function update(Draft $draft, array $data): Draft
    {
        if (array_key_exists('tags', $data)) {
            $data['tags'] = $this->tags($data['tags']);
        }
        if (array_key_exists('is_published', $data) && normalize_boolean($data['is_published'])) {
            $data['status'] = 'published';
            $data['published_at'] = $draft->published_at ?? now();
        }

        $draft->update($data);

        return $draft->fresh();
    }

    /**
     * Hapus draf.
     */
    public function destroy(Draft $draft): void
    {
        $draft->delete();
    }
}
