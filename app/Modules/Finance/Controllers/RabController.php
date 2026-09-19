<?php

declare(strict_types=1);

namespace App\Modules\Finance\Controllers;

use App\Http\Controllers\Api\ApiController;
use App\Modules\Finance\Models\RabItem;
use App\Modules\Finance\Services\RabService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * RabController
 *
 * Kontroler RESTful untuk resource item Rencana Anggaran Biaya (RAB).
 */
class RabController extends ApiController
{
    /**
     * Daftar item RAB dengan filter tipe/kategori.
     */
    public function index(Request $request, RabService $service): JsonResponse
    {
        $items = $service->index($request->query());

        return $this->ok($items, 'Daftar item RAB');
    }

    /**
     * Detail satu item RAB.
     */
    public function show(RabItem $item, RabService $service): JsonResponse
    {
        return $this->ok($service->show($item), 'Detail item RAB');
    }

    /**
     * Buat item RAB baru.
     */
    public function store(Request $request, RabService $service): JsonResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'string', 'in:income,expense,item'],
            'category' => ['nullable', 'string', 'max:255'],
            'unit' => ['nullable', 'string', 'max:50'],
            'quantity' => ['nullable', 'numeric', 'min:0'],
            'unit_price' => ['nullable', 'numeric', 'min:0'],
            'amount' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string'],
        ]);

        $item = $service->store($data);

        return $this->ok($item, 'Item RAB dibuat', 201);
    }

    /**
     * Perbarui item RAB yang sudah ada.
     */
    public function update(Request $request, RabItem $item, RabService $service): JsonResponse
    {
        $data = $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'type' => ['sometimes', 'string', 'in:income,expense,item'],
            'category' => ['nullable', 'string', 'max:255'],
            'unit' => ['nullable', 'string', 'max:50'],
            'quantity' => ['nullable', 'numeric', 'min:0'],
            'unit_price' => ['nullable', 'numeric', 'min:0'],
            'amount' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string'],
        ]);

        $updated = $service->update($item, $data);

        return $this->ok($updated, 'Item RAB diperbarui');
    }

    /**
     * Hapus item RAB.
     */
    public function destroy(RabItem $item, RabService $service): JsonResponse
    {
        $service->destroy($item);

        return $this->ok([], 'Item RAB dihapus');
    }

    /**
     * Ringkasan total pemasukan-pengeluaran RAB.
     */
    public function summary(RabService $service): JsonResponse
    {
        return $this->ok($service->summary(), 'Ringkasan RAB');
    }
}
