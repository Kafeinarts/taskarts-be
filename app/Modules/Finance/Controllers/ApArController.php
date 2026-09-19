<?php

declare(strict_types=1);

namespace App\Modules\Finance\Controllers;

use App\Http\Controllers\Api\ApiController;
use App\Modules\Finance\Models\ApArEntry;
use App\Modules\Finance\Services\ApArService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * ApArController
 *
 * Kontroler RESTful untuk resource Accounts Payable / Accounts Receivable.
 */
class ApArController extends ApiController
{
    /**
     * Daftar entri AP/AR dengan filter tipe/status.
     */
    public function index(Request $request, ApArService $service): JsonResponse
    {
        $entries = $service->index($request->query());

        return $this->ok($entries, 'Daftar AP/AR');
    }

    /**
     * Detail satu entri AP/AR.
     */
    public function show(ApArEntry $entry, ApArService $service): JsonResponse
    {
        return $this->ok($service->show($entry), 'Detail entri AP/AR');
    }

    /**
     * Buat entri AP/AR baru.
     */
    public function store(Request $request, ApArService $service): JsonResponse
    {
        $data = $request->validate([
            'type' => ['required', 'string', 'in:payable,receivable'],
            'contact_name' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'amount' => ['required', 'numeric', 'min:0'],
            'paid_amount' => ['nullable', 'numeric', 'min:0'],
            'due_date' => ['nullable', 'date'],
            'status' => ['nullable', 'string', 'in:open,partial,settled,overdue'],
        ]);

        $entry = $service->store($data);

        return $this->ok($entry, 'Entri AP/AR dibuat', 201);
    }

    /**
     * Perbarui entri AP/AR yang sudah ada.
     */
    public function update(Request $request, ApArEntry $entry, ApArService $service): JsonResponse
    {
        $data = $request->validate([
            'type' => ['sometimes', 'string', 'in:payable,receivable'],
            'contact_name' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'amount' => ['sometimes', 'numeric', 'min:0'],
            'due_date' => ['nullable', 'date'],
            'status' => ['nullable', 'string', 'in:open,partial,settled,overdue'],
        ]);

        $updated = $service->update($entry, $data);

        return $this->ok($updated, 'Entri AP/AR diperbarui');
    }

    /**
     * Hapus entri AP/AR.
     */
    public function destroy(ApArEntry $entry, ApArService $service): JsonResponse
    {
        $service->destroy($entry);

        return $this->ok([], 'Entri AP/AR dihapus');
    }

    /**
     * Catat pembayaran/pelunasan parsial terhadap entri.
     */
    public function settle(Request $request, ApArEntry $entry, ApArService $service): JsonResponse
    {
        $data = $request->validate([
            'amount' => ['required', 'numeric', 'min:0'],
        ]);

        return $this->ok($service->settle($entry, $data['amount']), 'Pembayaran dicatat');
    }

    /**
     * Ringkasan posisi hutang & piutang.
     */
    public function summary(ApArService $service): JsonResponse
    {
        return $this->ok($service->summary(), 'Ringkasan AP/AR');
    }
}
