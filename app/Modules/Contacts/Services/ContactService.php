<?php

declare(strict_types=1);

namespace App\Modules\Contacts\Services;

use App\Modules\Contacts\Models\Contact;
use App\Services\BaseService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

/**
 * ContactService
 *
 * Service modul Contacts untuk operasi CRUD, pencarian, dan filter favorit.
 */
class ContactService extends BaseService
{
    /**
     * Daftar kontak milik user dengan filter opsional.
     *
     * @param  array<string,mixed>  $filters  q, company, favorite
     */
    public function index(array $filters = []): Collection
    {
        return $this->own(Contact::query())
            ->when($filters['q'] ?? null, function (Builder $query, string $q) {
                return $query->where(fn (Builder $inner) => $inner
                    ->where('name', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%")
                    ->orWhere('company', 'like', "%{$q}%")
                    ->orWhere('phone', 'like', "%{$q}%"));
            })
            ->when($filters['company'] ?? null, fn (Builder $query, string $company) => $query->where('company', $company))
            ->when(\array_key_exists('favorite', $filters) && $filters['favorite'] !== null, fn (Builder $query) => $query->where('is_favorite', normalize_boolean($filters['favorite'])))
            ->orderBy('name')
            ->get();
    }

    /**
     * Ambil satu kontak.
     */
    public function show(Contact $contact): Contact
    {
        return $contact;
    }

    /**
     * Buat kontak baru.
     *
     * @param  array<string,mixed>  $data
     */
    public function store(array $data): Contact
    {
        $data['user_id'] = $this->userId();
        $data['tags'] = $this->tags($data['tags'] ?? []);

        return Contact::create($data);
    }

    /**
     * Perbarui kontak yang sudah ada.
     *
     * @param  array<string,mixed>  $data
     */
    public function update(Contact $contact, array $data): Contact
    {
        if (array_key_exists('tags', $data)) {
            $data['tags'] = $this->tags($data['tags']);
        }

        $contact->update($data);

        return $contact->fresh();
    }

    /**
     * Hapus kontak.
     */
    public function destroy(Contact $contact): void
    {
        $contact->delete();
    }

    /**
     * Rekapitulasi kontak: total, favorit, dan jumlah perusahaan unik.
     *
     * @return array<string,int>
     */
    public function stats(): array
    {
        $base = $this->own(Contact::query());

        return [
            'total' => (clone $base)->count(),
            'favorites' => (clone $base)->where('is_favorite', true)->count(),
            'companies' => (clone $base)->whereNotNull('company')->distinct('company')->count(),
        ];
    }
}
