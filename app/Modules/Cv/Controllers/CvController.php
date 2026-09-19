<?php

declare(strict_types=1);

namespace App\Modules\Cv\Controllers;

use App\Http\Controllers\Api\ApiController;
use App\Modules\Cv\Models\Cv;
use App\Modules\Cv\Services\CvService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * CvController
 *
 * Kontroler RESTful untuk resource CV/resume (data JSON) modul Cv.
 */
class CvController extends ApiController
{
    /**
     * Daftar CV milik user.
     */
    public function index(Request $request, CvService $service): JsonResponse
    {
        $default = $request->has('default') ? normalize_boolean($request->query('default')) : null;

        return $this->ok($service->index($default), 'Daftar CV');
    }

    /**
     * Detail satu CV.
     */
    public function show(Cv $cv, CvService $service): JsonResponse
    {
        return $this->ok($service->show($cv), 'Detail CV');
    }

    /**
     * Buat CV baru.
     */
    public function store(Request $request, CvService $service): JsonResponse
    {
        $data = $request->validate([
            'name' => ['nullable', 'string', 'max:255'],
            'data' => ['nullable', 'array'],
            'selected_template' => ['nullable', 'string', 'max:100'],
            'custom_color' => ['nullable', 'string', 'max:20'],
            'is_default' => ['nullable', 'boolean'],
        ]);

        $cv = $service->store($data);

        return $this->ok($cv, 'CV dibuat', 201);
    }

    /**
     * Perbarui CV yang sudah ada.
     */
    public function update(Request $request, Cv $cv, CvService $service): JsonResponse
    {
        $data = $request->validate([
            'name' => ['nullable', 'string', 'max:255'],
            'data' => ['nullable', 'array'],
            'selected_template' => ['nullable', 'string', 'max:100'],
            'custom_color' => ['nullable', 'string', 'max:20'],
            'is_default' => ['nullable', 'boolean'],
        ]);

        return $this->ok($service->update($cv, $data), 'CV diperbarui');
    }

    /**
     * Hapus CV.
     */
    public function destroy(Cv $cv, CvService $service): JsonResponse
    {
        $service->destroy($cv);

        return $this->ok([], 'CV dihapus');
    }

    /**
     * Tetapkan satu CV sebagai default.
     */
    public function setDefault(Cv $cv, CvService $service): JsonResponse
    {
        return $this->ok($service->setDefault($cv), 'CV diset sebagai default');
    }
}
