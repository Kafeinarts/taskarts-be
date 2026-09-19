<?php

declare(strict_types=1);

namespace App\Modules\Reports\Controllers;

use App\Http\Controllers\Api\ApiController;
use App\Modules\Contacts\Models\Contact;
use App\Modules\Contacts\Services\ContactService;
use App\Modules\Finance\Models\FinanceTransaction;
use App\Modules\Finance\Models\RabItem;
use App\Modules\Finance\Services\FinanceTransactionService;
use App\Modules\Finance\Services\RabService;
use App\Modules\Tasks\Models\Task;
use App\Modules\Tasks\Services\TaskService;
use App\Services\Export\ExcelExportService;
use App\Services\Export\PdfExportService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

/**
 * ReportController
 *
 * Titik keluar (endpoint) untuk ekspor data lintas modul ke Excel dan PDF.
 * Data disusun menjadi pasangan heading + row generik sehingga service
 * export dapat dipakai ulang.
 */
class ReportController extends ApiController
{
    /**
     * Injeksi service export yang dibutuhkan.
     */
    public function __construct(
        private readonly ExcelExportService $excel,
        private readonly PdfExportService $pdf,
    ) {}

    /**
     * Ekspor daftar tugas ke Excel.
     */
    public function tasksExcel(Request $request, TaskService $service): BinaryFileResponse
    {
        $tasks = $service->index($request->query());

        return $this->excel->download(
            'Daftar Tugas',
            self::taskHeadings(),
            $tasks->map(fn (Task $task): array => self::taskRow($task))->all(),
        );
    }

    /**
     * Ekspor daftar tugas ke PDF.
     */
    public function tasksPdf(Request $request, TaskService $service): Response
    {
        $tasks = $service->index($request->query());

        return $this->pdf->table(
            'Laporan Daftar Tugas',
            self::taskHeadings(),
            $tasks->map(fn (Task $task): array => self::taskRow($task))->all(),
            ['Jumlah Data' => "{$tasks->count()} tugas"],
        );
    }

    /**
     * Ekspor daftar kontak ke Excel.
     */
    public function contactsExcel(Request $request, ContactService $service): BinaryFileResponse
    {
        $contacts = $service->index($request->query());

        return $this->excel->download(
            'Daftar Kontak',
            self::contactHeadings(),
            $contacts->map(fn (Contact $contact): array => self::contactRow($contact))->all(),
        );
    }

    /**
     * Ekspor daftar kontak ke PDF.
     */
    public function contactsPdf(Request $request, ContactService $service): Response
    {
        $contacts = $service->index($request->query());

        return $this->pdf->table(
            'Laporan Daftar Kontak',
            self::contactHeadings(),
            $contacts->map(fn (Contact $contact): array => self::contactRow($contact))->all(),
            ['Jumlah Data' => "{$contacts->count()} kontak"],
        );
    }

    /**
     * Ekspor transaksi keuangan ke Excel.
     */
    public function financeExcel(Request $request, FinanceTransactionService $service): BinaryFileResponse
    {
        $transactions = $service->index($request->query());

        return $this->excel->download(
            'Transaksi Keuangan',
            self::financeHeadings(),
            $transactions->map(fn (FinanceTransaction $tx): array => self::financeRow($tx))->all(),
        );
    }

    /**
     * Ekspor transaksi keuangan ke PDF.
     */
    public function financePdf(Request $request, FinanceTransactionService $service): Response
    {
        $transactions = $service->index($request->query());

        return $this->pdf->table(
            'Laporan Transaksi Keuangan',
            self::financeHeadings(),
            $transactions->map(fn (FinanceTransaction $tx): array => self::financeRow($tx))->all(),
            ['Jumlah Data' => "{$transactions->count()} transaksi"],
        );
    }

    /**
     * Ekspor item RAB ke Excel.
     */
    public function rabExcel(Request $request, RabService $service): BinaryFileResponse
    {
        $items = $service->index($request->query());

        return $this->excel->download(
            'Item RAB',
            self::rabHeadings(),
            $items->map(fn (RabItem $item): array => self::rabRow($item))->all(),
        );
    }

    /**
     * Headings standar untuk laporan tugas.
     *
     * @return array<int, string>
     */
    private static function taskHeadings(): array
    {
        return ['ID', 'Judul', 'Proyek', 'Prioritas', 'Status', 'Kategori', 'Tenggat', 'Selesai', 'Waktu (menit)'];
    }

    /**
     * Konversi satu tugas menjadi baris laporan.
     *
     * @return array<int, mixed>
     */
    private static function taskRow(Task $task): array
    {
        return [
            $task->id,
            $task->title,
            $task->project?->name ?? '-',
            $task->priority,
            $task->status,
            $task->category ?? '-',
            (string) $task->due_date,
            $task->is_completed ? 'Ya' : 'Tidak',
            $task->time_spent_minutes,
        ];
    }

    /**
     * Headings standar untuk laporan kontak.
     *
     * @return array<int, string>
     */
    private static function contactHeadings(): array
    {
        return ['ID', 'Nama', 'Perusahaan', 'Posisi', 'Telepon', 'Email', 'Favorit'];
    }

    /**
     * Konversi satu kontak menjadi baris laporan.
     *
     * @return array<int, mixed>
     */
    private static function contactRow(Contact $contact): array
    {
        return [
            $contact->id,
            $contact->name,
            $contact->company ?? '-',
            $contact->role ?? '-',
            $contact->phone ?? '-',
            $contact->email ?? '-',
            $contact->is_favorite ? 'Ya' : 'Tidak',
        ];
    }

    /**
     * Headings standar untuk laporan transaksi keuangan.
     *
     * @return array<int, string>
     */
    private static function financeHeadings(): array
    {
        return ['ID', 'Tanggal', 'Tipe', 'Kategori', 'Jumlah', 'Metode', 'Status', 'Deskripsi'];
    }

    /**
     * Konversi satu transaksi menjadi baris laporan.
     *
     * @return array<int, mixed>
     */
    private static function financeRow(FinanceTransaction $transaction): array
    {
        return [
            $transaction->id,
            (string) $transaction->date,
            $transaction->type,
            $transaction->category?->name ?? '-',
            $transaction->amount,
            $transaction->payment_method ?? '-',
            $transaction->status,
            $transaction->description ?? '-',
        ];
    }

    /**
     * Headings standar untuk laporan RAB.
     *
     * @return array<int, string>
     */
    private static function rabHeadings(): array
    {
        return ['ID', 'Nama', 'Tipe', 'Kategori', 'Satuan', 'Qty', 'Harga Satuan', 'Jumlah', 'Total'];
    }

    /**
     * Konversi satu item RAB menjadi baris laporan.
     *
     * @return array<int, mixed>
     */
    private static function rabRow(RabItem $item): array
    {
        return [
            $item->id,
            $item->name,
            $item->type,
            $item->category ?? '-',
            $item->unit ?? '-',
            $item->quantity,
            $item->unit_price,
            $item->amount,
            $item->calculated_amount,
        ];
    }
}
