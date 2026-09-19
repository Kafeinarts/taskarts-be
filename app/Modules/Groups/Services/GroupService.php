<?php

declare(strict_types=1);

namespace App\Modules\Groups\Services;

use App\Models\User;
use App\Modules\Groups\Models\Group;
use Illuminate\Database\Eloquent\Collection;

/**
 * GroupService
 *
 * Service untuk CRUD grup/teams dan manajemen anggota.
 * Grup digunakan untuk membagi user ke dalam tim kerja.
 */
class GroupService
{
    /**
     * Daftar semua grup dengan jumlah anggota.
     */
    public function index(): Collection
    {
        return Group::withCount('members')->orderBy('name')->get();
    }

    /**
     * Detail satu grup beserta daftar anggota.
     */
    public function show(Group $group): Group
    {
        return $group->load('members:id,name,email,role');
    }

    /**
     * Buat grup baru.
     *
     * @param  array{name: string, description?: string}  $data
     */
    public function store(array $data): Group
    {
        return Group::create($data);
    }

    /**
     * Perbarui data grup.
     *
     * @param  array<string,mixed>  $data
     */
    public function update(Group $group, array $data): Group
    {
        $group->update($data);

        return $group->fresh();
    }

    /**
     * Hapus grup beserta relasi pivot.
     */
    public function destroy(Group $group): void
    {
        $group->members()->detach();
        $group->delete();
    }

    /**
     * Tambahkan user ke dalam grup.
     * Jika role tidak disediakan, default 'member'.
     */
    public function addMember(Group $group, User $user, string $role = 'member'): Group
    {
        $group->members()->syncWithoutDetaching([
            $user->id => ['role' => $role],
        ]);

        return $group->fresh(['members']);
    }

    /**
     * Hapus user dari grup.
     */
    public function removeMember(Group $group, User $user): Group
    {
        $group->members()->detach($user->id);

        return $group->fresh(['members']);
    }

    /**
     * Perbarui role anggota dalam grup.
     */
    public function updateMemberRole(Group $group, User $user, string $role): Group
    {
        $group->members()->updateExistingPivot($user->id, ['role' => $role]);

        return $group->fresh(['members']);
    }

    /**
     * Daftar grup yang diikuti oleh user tertentu.
     */
    public function userGroups(User $user): Collection
    {
        return $user->groups()->withCount('members')->get();
    }
}
