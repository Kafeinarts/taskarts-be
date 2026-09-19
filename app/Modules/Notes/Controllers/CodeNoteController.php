<?php

declare(strict_types=1);

namespace App\Modules\Notes\Controllers;

use App\Http\Controllers\Api\ApiController;
use App\Modules\Notes\Models\CodeNote;
use App\Modules\Notes\Services\CodeNoteService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * CodeNoteController
 *
 * Kontroler RESTful untuk resource catatan kode / snippets modul Notes.
 */
class CodeNoteController extends ApiController
{
    /**
     * Daftar catatan kode dengan filter opsional.
     */
    public function index(Request $request, CodeNoteService $service): JsonResponse
    {
        return $this->ok($service->index($request->query()), 'Daftar catatan kode');
    }

    /**
     * Detail satu catatan kode.
     */
    public function show(CodeNote $note, CodeNoteService $service): JsonResponse
    {
        return $this->ok($service->show($note), 'Detail catatan kode');
    }

    /**
     * Buat catatan kode baru.
     */
    public function store(Request $request, CodeNoteService $service): JsonResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'content' => ['nullable', 'string'],
            'language' => ['nullable', 'string', 'max:50'],
            'tags' => ['nullable'],
            'is_pinned' => ['nullable', 'boolean'],
            'is_favorite' => ['nullable', 'boolean'],
            'is_archived' => ['nullable', 'boolean'],
        ]);

        $note = $service->store($data);

        return $this->ok($note, 'Catatan kode dibuat', 201);
    }

    /**
     * Perbarui catatan kode yang sudah ada.
     */
    public function update(Request $request, CodeNote $note, CodeNoteService $service): JsonResponse
    {
        $data = $request->validate([
            'title' => ['sometimes', 'string', 'max:255'],
            'content' => ['nullable', 'string'],
            'language' => ['nullable', 'string', 'max:50'],
            'tags' => ['nullable'],
            'is_pinned' => ['nullable', 'boolean'],
            'is_favorite' => ['nullable', 'boolean'],
            'is_archived' => ['nullable', 'boolean'],
        ]);

        return $this->ok($service->update($note, $data), 'Catatan kode diperbarui');
    }

    /**
     * Hapus catatan kode.
     */
    public function destroy(CodeNote $note, CodeNoteService $service): JsonResponse
    {
        $service->destroy($note);

        return $this->ok([], 'Catatan kode dihapus');
    }
}
