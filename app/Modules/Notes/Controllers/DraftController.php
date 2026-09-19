<?php

declare(strict_types=1);

namespace App\Modules\Notes\Controllers;

use App\Http\Controllers\Api\ApiController;
use App\Modules\Notes\Models\Draft;
use App\Modules\Notes\Services\DraftService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * DraftController
 *
 * Kontroler RESTful untuk resource draf tulisan / Medium-style drafts.
 */
class DraftController extends ApiController
{
    /**
     * Daftar draf dengan filter opsional.
     */
    public function index(Request $request, DraftService $service): JsonResponse
    {
        return $this->ok($service->index($request->query()), 'Daftar draf');
    }

    /**
     * Detail satu draf.
     */
    public function show(Draft $draft, DraftService $service): JsonResponse
    {
        return $this->ok($service->show($draft), 'Detail draf');
    }

    /**
     * Buat draf baru.
     */
    public function store(Request $request, DraftService $service): JsonResponse
    {
        $data = $request->validate([
            'title' => ['nullable', 'string', 'max:255'],
            'content' => ['nullable', 'string'],
            'type' => ['nullable', 'string', 'in:article,story,note,post'],
            'status' => ['nullable', 'string', 'in:draft,published,archived'],
            'tags' => ['nullable'],
            'read_time' => ['nullable', 'integer', 'min:0'],
            'canonical_url' => ['nullable', 'string', 'max:255'],
            'is_published' => ['nullable', 'boolean'],
        ]);

        $draft = $service->store($data);

        return $this->ok($draft, 'Draf dibuat', 201);
    }

    /**
     * Perbarui draf yang sudah ada.
     */
    public function update(Request $request, Draft $draft, DraftService $service): JsonResponse
    {
        $data = $request->validate([
            'title' => ['nullable', 'string', 'max:255'],
            'content' => ['nullable', 'string'],
            'type' => ['nullable', 'string', 'in:article,story,note,post'],
            'status' => ['nullable', 'string', 'in:draft,published,archived'],
            'tags' => ['nullable'],
            'read_time' => ['nullable', 'integer', 'min:0'],
            'canonical_url' => ['nullable', 'string', 'max:255'],
            'is_published' => ['nullable', 'boolean'],
        ]);

        return $this->ok($service->update($draft, $data), 'Draf diperbarui');
    }

    /**
     * Hapus draf.
     */
    public function destroy(Draft $draft, DraftService $service): JsonResponse
    {
        $service->destroy($draft);

        return $this->ok([], 'Draf dihapus');
    }
}
