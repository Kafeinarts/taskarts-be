<?php

declare(strict_types=1);

namespace App\Modules\Notes\Controllers;

use App\Http\Controllers\Api\ApiController;
use App\Modules\Notes\Models\DiaryEntry;
use App\Modules\Notes\Services\DiaryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * DiaryController
 *
 * Kontroler RESTful untuk resource jurnal / buku harian modul Notes.
 */
class DiaryController extends ApiController
{
    /**
     * Daftar entri jurnal dengan filter opsional.
     */
    public function index(Request $request, DiaryService $service): JsonResponse
    {
        return $this->ok($service->index($request->query()), 'Daftar jurnal');
    }

    /**
     * Detail satu entri jurnal.
     */
    public function show(DiaryEntry $entry, DiaryService $service): JsonResponse
    {
        return $this->ok($service->show($entry), 'Detail jurnal');
    }

    /**
     * Buat entri jurnal baru.
     */
    public function store(Request $request, DiaryService $service): JsonResponse
    {
        $data = $request->validate([
            'title' => ['nullable', 'string', 'max:255'],
            'content' => ['nullable', 'string'],
            'mood' => ['nullable', 'string', 'max:50'],
            'mood_label' => ['nullable', 'string', 'max:100'],
            'weather' => ['nullable', 'string', 'max:50'],
            'weather_label' => ['nullable', 'string', 'max:100'],
            'location' => ['nullable', 'string', 'max:255'],
            'category' => ['nullable', 'string', 'max:255'],
            'tags' => ['nullable'],
            'is_pinned' => ['nullable', 'boolean'],
            'is_favorite' => ['nullable', 'boolean'],
            'is_private' => ['nullable', 'boolean'],
            'entry_date' => ['nullable', 'date'],
            'entry_time' => ['nullable', 'date_format:H:i:s'],
        ]);

        $entry = $service->store($data);

        return $this->ok($entry, 'Jurnal dibuat', 201);
    }

    /**
     * Perbarui entri jurnal yang sudah ada.
     */
    public function update(Request $request, DiaryEntry $entry, DiaryService $service): JsonResponse
    {
        $data = $request->validate([
            'title' => ['nullable', 'string', 'max:255'],
            'content' => ['nullable', 'string'],
            'mood' => ['nullable', 'string', 'max:50'],
            'mood_label' => ['nullable', 'string', 'max:100'],
            'weather' => ['nullable', 'string', 'max:50'],
            'weather_label' => ['nullable', 'string', 'max:100'],
            'location' => ['nullable', 'string', 'max:255'],
            'category' => ['nullable', 'string', 'max:255'],
            'tags' => ['nullable'],
            'is_pinned' => ['nullable', 'boolean'],
            'is_favorite' => ['nullable', 'boolean'],
            'is_private' => ['nullable', 'boolean'],
            'entry_date' => ['sometimes', 'date'],
            'entry_time' => ['nullable', 'date_format:H:i:s'],
        ]);

        return $this->ok($service->update($entry, $data), 'Jurnal diperbarui');
    }

    /**
     * Hapus entri jurnal.
     */
    public function destroy(DiaryEntry $entry, DiaryService $service): JsonResponse
    {
        $service->destroy($entry);

        return $this->ok([], 'Jurnal dihapus');
    }
}
