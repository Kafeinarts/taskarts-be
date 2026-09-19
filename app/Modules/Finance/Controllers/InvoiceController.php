<?php

declare(strict_types=1);

namespace App\Modules\Finance\Controllers;

use App\Http\Controllers\Api\ApiController;
use App\Modules\Finance\Models\Invoice;
use App\Modules\Finance\Services\InvoiceService;
use App\Services\Export\PdfExportService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * InvoiceController
 *
 * Kontroler RESTful untuk resource invoice/faktur modul Finance,
 * termasuk pencatatan pembayaran dan ekspor PDF faktur.
 */
class InvoiceController extends ApiController
{
    /**
     * Daftar invoice dengan filter opsional (status, payment_status, q).
     */
    public function index(Request $request, InvoiceService $service): JsonResponse
    {
        $invoices = $service->index($request->query());

        return $this->ok($invoices, 'Daftar invoice');
    }

    /**
     * Detail satu invoice.
     */
    public function show(Invoice $invoice, InvoiceService $service): JsonResponse
    {
        return $this->ok($service->show($invoice), 'Detail invoice');
    }

    /**
     * Buat invoice baru (total dihitung otomatis dari items).
     */
    public function store(Request $request, InvoiceService $service): JsonResponse
    {
        $data = $request->validate([
            'number' => ['nullable', 'string', 'max:50', 'unique:invoices'],
            'customer_name' => ['nullable', 'string', 'max:255'],
            'customer_email' => ['nullable', 'email', 'max:255'],
            'customer_phone' => ['nullable', 'string', 'max:50'],
            'customer_company' => ['nullable', 'string', 'max:255'],
            'issue_date' => ['required', 'date'],
            'due_date' => ['nullable', 'date'],
            'status' => ['nullable', 'string', 'in:draft,sent,paid,overdue,cancelled'],
            'payment_status' => ['nullable', 'string', 'in:unpaid,partial,paid,refunded'],
            'currency' => ['nullable', 'string', 'max:10'],
            'tax' => ['nullable', 'numeric', 'min:0'],
            'discount' => ['nullable', 'numeric', 'min:0'],
            'items' => ['nullable', 'array'],
            'notes' => ['nullable', 'string'],
            'terms' => ['nullable', 'string'],
        ]);

        $invoice = $service->store($data);

        return $this->ok($invoice, 'Invoice dibuat', 201);
    }

    /**
     * Perbarui invoice yang sudah ada.
     */
    public function update(Request $request, Invoice $invoice, InvoiceService $service): JsonResponse
    {
        $data = $request->validate([
            'number' => ['sometimes', 'string', 'max:50', 'unique:invoices,number,'.$invoice->id],
            'customer_name' => ['nullable', 'string', 'max:255'],
            'customer_email' => ['nullable', 'email', 'max:255'],
            'customer_phone' => ['nullable', 'string', 'max:50'],
            'customer_company' => ['nullable', 'string', 'max:255'],
            'issue_date' => ['sometimes', 'date'],
            'due_date' => ['nullable', 'date'],
            'status' => ['nullable', 'string', 'in:draft,sent,paid,overdue,cancelled'],
            'currency' => ['nullable', 'string', 'max:10'],
            'tax' => ['nullable', 'numeric', 'min:0'],
            'discount' => ['nullable', 'numeric', 'min:0'],
            'items' => ['nullable', 'array'],
            'notes' => ['nullable', 'string'],
            'terms' => ['nullable', 'string'],
        ]);

        $updated = $service->update($invoice, $data);

        return $this->ok($updated, 'Invoice diperbarui');
    }

    /**
     * Hapus invoice.
     */
    public function destroy(Invoice $invoice, InvoiceService $service): JsonResponse
    {
        $service->destroy($invoice);

        return $this->ok([], 'Invoice dihapus');
    }

    /**
     * Statistik invoice berdasarkan status pembayaran.
     */
    public function stats(InvoiceService $service): JsonResponse
    {
        return $this->ok($service->stats(), 'Statistik invoice');
    }

    /**
     * Catat pembayaran terhadap invoice.
     */
    public function pay(Request $request, Invoice $invoice, InvoiceService $service): JsonResponse
    {
        $data = $request->validate([
            'amount' => ['required', 'numeric', 'min:0'],
        ]);

        return $this->ok($service->recordPayment($invoice, $data['amount']), 'Pembayaran dicatat');
    }

    /**
     * Unduh invoice dalam format PDF.
     */
    public function pdf(Invoice $invoice, PdfExportService $service): Response
    {
        return $service->invoice($invoice);
    }
}
