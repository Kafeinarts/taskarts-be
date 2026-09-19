<?php

declare(strict_types=1);

namespace App\Services\Export;

use App\Modules\Finance\Models\Invoice;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;
use Illuminate\Support\Str;

/**
 * PdfExportService
 *
 * Layanan ekspor dokumen PDF memakai laravel-dompdf (barryvdh/laravel-dompdf).
 * Menyediakan dua bentuk: tabel generik dan faktur (invoice) yang terformat.
 */
class PdfExportService
{
    /**
     * Render tabel generik menjadi PDF dan unduh.
     *
     * @param  array<int, string>  $headings  judul kolom
     * @param  array<int, array<int, mixed>>  $rows  isi baris
     * @param  array<string, mixed>  $meta  info tambahan (periode, user, dll)
     */
    public function table(
        string $title,
        array $headings,
        array $rows,
        array $meta = [],
        ?string $filename = null,
        string $orientation = 'portrait',
    ): Response {
        $stamp = now()->format('Ymd-His');
        $fileName = $filename ?? Str::slug($title).'-'.$stamp.'.pdf';

        $pdf = Pdf::loadView('pdf.table', compact('title', 'headings', 'rows', 'meta'))
            ->setPaper('a4', $orientation);

        return $pdf->download($fileName);
    }

    /**
     * Render faktur (invoice) berformat dokumen resmi dan unduh.
     */
    public function invoice(Invoice $invoice, ?string $filename = null): Response
    {
        $invoice->load('user');
        $fileName = $filename ?? 'invoice-'.$invoice->number.'-'.now()->format('Ymd-His').'.pdf';

        $pdf = Pdf::loadView('pdf.invoice', compact('invoice'))
            ->setPaper('a4');

        return $pdf->download($fileName);
    }

    /**
     * Render tabel generik menjadi PDF dan tampilkan inline di browser.
     *
     * @param  array<int, string>  $headings  judul kolom
     * @param  array<int, array<int, mixed>>  $rows  isi baris
     * @param  array<string, mixed>  $meta  info tambahan
     */
    public function preview(
        string $title,
        array $headings,
        array $rows,
        array $meta = [],
    ): Response {
        $pdf = Pdf::loadView('pdf.table', compact('title', 'headings', 'rows', 'meta'))
            ->setPaper('a4');

        return $pdf->stream();
    }
}
