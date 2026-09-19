<?php

declare(strict_types=1);

namespace App\Modules\Groups\Controllers;

use App\Http\Controllers\Api\ApiController;
use App\Models\User;
use App\Modules\Groups\Models\Group;
use App\Modules\Groups\Services\GroupService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * GroupController
 *
 * Kontroler RESTful untuk manajemen grup/teams dan anggota.
 * Hanya admin yang boleh membuat/menghapus grup.
 * Semua user bisa melihat daftar grup.
 */
class GroupController extends ApiController
{
    /**
     * Daftar semua grup.
     */
    public function index(GroupService $service): JsonResponse
    {
        return $this->ok($service->index(), 'Daftar grup');
    }

    /**
     * Detail grup beserta anggota.
     */
    public function show(Group $group, GroupService $service): JsonResponse
    {
        return $this->ok($service->show($group), 'Detail grup');
    }

    /**
     * Buat grup baru (hanya admin).
     */
    public function store(Request $request, GroupService $service): JsonResponse
    {
        $this->ensureAdmin($request);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:100', 'unique:groups'],
            'description' => ['nullable', 'string'],
        ]);

        $group = $service->store($data);

        return $this->ok($group, 'Grup dibuat', 201);
    }

    /**
     * Perbarui data grup (hanya admin).
     */
    public function update(Request $request, Group $group, GroupService $service): JsonResponse
    {
        $this->ensureAdmin($request);

        $data = $request->validate([
            'name' => ['sometimes', 'string', 'max:100', 'unique:groups,name,'.$group->id],
            'description' => ['nullable', 'string'],
        ]);

        return $this->ok($service->update($group, $data), 'Grup diperbarui');
    }

    /**
     * Hapus grup (hanya admin).
     */
    public function destroy(Request $request, Group $group, GroupService $service): JsonResponse
    {
        $this->ensureAdmin($request);

        $service->destroy($group);

        return $this->ok([], 'Grup dihapus');
    }

    /**
     * Tambahkan anggota ke grup (hanya admin).
     */
    public function addMember(Request $request, Group $group, GroupService $service): JsonResponse
    {
        $this->ensureAdmin($request);

        $data = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'role' => ['nullable', 'string', 'in:admin,member,viewer'],
        ]);

        $user = User::findOrFail($data['user_id']);

        return $this->ok(
            $service->addMember($group, $user, $data['role'] ?? 'member'),
            'Anggota ditambahkan',
        );
    }

    /**
     * Hapus anggota dari grup (hanya admin).
     */
    public function removeMember(Request $request, Group $group, User $user, GroupService $service): JsonResponse
    {
        $this->ensureAdmin($request);

        return $this->ok(
            $service->removeMember($group, $user),
            'Anggota dihapus dari grup',
        );
    }

    /**
     * Perbarui role anggota dalam grup (hanya admin).
     */
    public function updateMemberRole(Request $request, Group $group, User $user, GroupService $service): JsonResponse
    {
        $this->ensureAdmin($request);

        $data = $request->validate([
            'role' => ['required', 'string', 'in:admin,member,viewer'],
        ]);

        return $this->ok(
            $service->updateMemberRole($group, $user, $data['role']),
            'Role anggota diperbarui',
        );
    }

    /**
     * Daftar grup yang diikuti oleh user yang sedang login.
     */
    public function myGroups(Request $request, GroupService $service): JsonResponse
    {
        return $this->ok(
            $service->userGroups($request->user()),
            'Grup saya',
        );
    }

    /**
     * Pastikan user yang melakukan request adalah admin.
     */
    private function ensureAdmin(Request $request): void
    {
        if (! $request->user()->isAdmin()) {
            abort(403, 'Hanya admin yang dapat melakukan aksi ini.');
        }
    }
}
