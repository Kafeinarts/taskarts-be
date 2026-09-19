<?php

declare(strict_types=1);

namespace App\Services\Export;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

/**
 * GenericTableExport
 *
 * Implementasi export Excel generik untuk semua modul. Data berupa array
 * baris + judul kolom sehingga dapat dipakai ulang lintas modul.
 */
class GenericTableExport implements FromArray, WithHeadings, WithStyles
{
    /**
     * @param  array<int, array<int, mixed>>  $rows  baris data
     * @param  array<int, string>  $headings  judul kolom
     */
    public function __construct(
        private readonly array $rows = [],
        private readonly array $headings = [],
    ) {}

    /**
     * Neraca kolom (heading) untuk baris pertama worksheet.
     *
     * @return array<int, string>
     */
    public function headings(): array
    {
        return $this->headings;
    }

    /**
     * Sumber data tabular untuk worksheet.
     *
     * @return array<int, array<int, mixed>>
     */
    public function array(): array
    {
        return $this->rows;
    }

    /**
     * Gaya dasar worksheet: tebalkan baris header.
     */
    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
