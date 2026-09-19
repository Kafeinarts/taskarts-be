<?php

declare(strict_types=1);

namespace App\Modules\Notes\Controllers;

use App\Http\Controllers\Api\ApiController;
use App\Modules\Notes\Models\Note;
use App\Modules\Notes\Services\NoteService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * NoteController
 *
 * Kontroler RESTful untuk resource catatan umum / sticky notes modul Notes.
 */
class NoteController extends ApiController
{
    /**
     * Daftar catatan dengan filter opsional.
     */
    public function index(Request $request, NoteService $service): JsonResponse
    {
        return $this->ok($service->index($request->query()), 'Daftar catatan');
    }

    /**
     * Detail satu catatan.
     */
    public function show(Note $note, NoteService $service): JsonResponse
    {
        return $this->ok($service->show($note), 'Detail catatan');
    }

    /**
     * Buat catatan baru.
     */
    public function store(Request $request, NoteService $service): JsonResponse
    {
        $data = $request->validate([
            'title' => ['nullable', 'string', 'max:255'],
            'content' => ['nullable', 'string'],
            'color' => ['nullable', 'string', 'max:20'],
            'category' => ['nullable', 'string', 'max:255'],
            'tags' => ['nullable'],
            'is_pinned' => ['nullable', 'boolean'],
            'is_archived' => ['nullable', 'boolean'],
        ]);

        $note = $service->store($data);

        return $this->ok($note, 'Catatan dibuat', 201);
    }

    /**
     * Perbarui catatan yang sudah ada.
     */
    public function update(Request $request, Note $note, NoteService $service): JsonResponse
    {
        $data = $request->validate([
            'title' => ['nullable', 'string', 'max:255'],
            'content' => ['nullable', 'string'],
            'color' => ['nullable', 'string', 'max:20'],
            'category' => ['nullable', 'string', 'max:255'],
            'tags' => ['nullable'],
            'is_pinned' => ['nullable', 'boolean'],
            'is_archived' => ['nullable', 'boolean'],
        ]);

        return $this->ok($service->update($note, $data), 'Catatan diperbarui');
    }

    /**
     * Hapus catatan.
     */
    public function destroy(Note $note, NoteService $service): JsonResponse
    {
        $service->destroy($note);

        return $this->ok([], 'Catatan dihapus');
    }
}
