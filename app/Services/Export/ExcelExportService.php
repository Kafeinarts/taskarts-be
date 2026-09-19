<?php

declare(strict_types=1);

namespace App\Services\Export;

use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

/**
 * ExcelExportService
 *
 * Layanan ekspor data tabular ke format Excel (.xlsx) memakai laravel-excel.
 * Data disusun sebagai pasangan headings + rows dari modul pemanggil.
 */
class ExcelExportService
{
    /**
     * Unduh tabel data sebagai file Excel.
     *
     * @param  array<int, string>  $headings  judul kolom
     * @param  array<int, array<int, mixed>>  $rows  isi baris
     */
    public function download(
        string $sheetName,
        array $headings,
        array $rows,
        ?string $filename = null,
    ): BinaryFileResponse {
        $export = new GenericTableExport($rows, $headings);
        $fileName = $filename ?? $this->filename($sheetName);

        return Excel::download($export, $fileName);
    }

    /**
     * Buat nama file default dengan timestamp, mis. `tasks-20260918-153000.xlsx`.
     */
    protected function filename(string $sheetName): string
    {
        $slug = Str::slug($sheetName);

        return $slug.'-'.now()->format('Ymd-His').'.xlsx';
    }
}
