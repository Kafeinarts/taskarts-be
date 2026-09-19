<?php

declare(strict_types=1);

namespace App\Modules\Finance\Controllers;

use App\Http\Controllers\Api\ApiController;
use App\Modules\Finance\Models\FinanceCategory;
use App\Modules\Finance\Services\FinanceCategoryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * FinanceCategoryController
 *
 * Kontroler RESTful untuk resource kategori keuangan modul Finance.
 */
class FinanceCategoryController extends ApiController
{
    /**
     * Daftar kategori, opsional filter berdasarkan tipe (income/expense).
     */
    public function index(Request $request, FinanceCategoryService $service): JsonResponse
    {
        $categories = $service->index($request->query('type'));

        return $this->ok($categories, 'Daftar kategori keuangan');
    }

    /**
     * Detail satu kategori.
     */
    public function show(FinanceCategory $category, FinanceCategoryService $service): JsonResponse
    {
        return $this->ok($service->show($category), 'Detail kategori');
    }

    /**
     * Buat kategori baru.
     */
    public function store(Request $request, FinanceCategoryService $service): JsonResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'type' => ['nullable', 'string', 'in:income,expense'],
            'icon' => ['nullable', 'string', 'max:100'],
            'color' => ['nullable', 'string', 'max:20'],
        ]);

        $category = $service->store($data);

        return $this->ok($category, 'Kategori dibuat', 201);
    }

    /**
     * Perbarui kategori yang sudah ada.
     */
    public function update(Request $request, FinanceCategory $category, FinanceCategoryService $service): JsonResponse
    {
        $data = $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'type' => ['nullable', 'string', 'in:income,expense'],
            'icon' => ['nullable', 'string', 'max:100'],
            'color' => ['nullable', 'string', 'max:20'],
        ]);

        $updated = $service->update($category, $data);

        return $this->ok($updated, 'Kategori diperbarui');
    }

    /**
     * Hapus kategori.
     */
    public function destroy(FinanceCategory $category, FinanceCategoryService $service): JsonResponse
    {
        $service->destroy($category);

        return $this->ok([], 'Kategori dihapus');
    }
}
