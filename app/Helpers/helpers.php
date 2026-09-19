<?php

declare(strict_types=1);

use Illuminate\Support\Str;

/*
|--------------------------------------------------------------------------
| Global Helper Functions — TaskArts Backend
|--------------------------------------------------------------------------
|
| Kumpulan fungsi bantuan global yang dipakai lintas modul. Semua fungsi
| dibungkus `function_exists()` agar aman bila terjadi double-loading.
|
*/

if (! function_exists('app_name')) {
    /**
     * Ambil nama aplikasi dari konfigurasi.
     */
    function app_name(): string
    {
        return (string) config('app.name', 'TaskArts');
    }
}

if (! function_exists('current_user_id')) {
    /**
     * Ambil ID user yang sedang login (null bila tidak ada sesi auth).
     */
    function current_user_id(): ?int
    {
        return auth('sanctum')->id() ?? auth()->id();
    }
}

if (! function_exists('money_value')) {
    /**
     * Normalisasi nilai menjadi float 2 desimal.
     */
    function money_value(mixed $value): float
    {
        return round((float) $value, 2);
    }
}

if (! function_exists('format_rupiah')) {
    /**
     * Format angka menjadi Rupiah (mis. Rp1.250.000).
     */
    function format_rupiah(float $amount, bool $withSymbol = true): string
    {
        $formatted = number_format(money_value($amount), 0, ',', '.');

        return $withSymbol ? 'Rp'.$formatted : $formatted;
    }
}

if (! function_exists('format_date')) {
    /**
     * Format tanggal (oleh Carbon) dengan format tertentu; aman terhadap nilai kosong.
     */
    function format_date(mixed $date, string $format = 'Y-m-d'): ?string
    {
        if (empty($date)) {
            return null;
        }

        $parsed = $date instanceof DateTimeInterface ? $date : Carbon\Carbon::parse($date);

        return $parsed->format($format);
    }
}

if (! function_exists('normalize_tags')) {
    /**
     * Normalisasi input tags (array, string koma, atau JSON string) menjadi array string bersih.
     *
     * @return array<int, string>
     */
    function normalize_tags(mixed $tags): array
    {
        if (is_string($tags)) {
            $decoded = json_decode($tags, true);

            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                $tags = $decoded;
            } else {
                $tags = array_map('trim', explode(',', $tags));
            }
        }

        if (! is_array($tags)) {
            return [];
        }

        return array_values(array_unique(array_map(
            fn (mixed $tag): string => (string) $tag,
            array_filter($tags, fn (mixed $tag): bool => trim((string) $tag) !== ''),
        )));
    }
}

if (! function_exists('generate_invoice_number')) {
    /**
     * Buat nomor faktur unik, misalnya `INV-20260918-AB12CD`.
     */
    function generate_invoice_number(): string
    {
        return 'INV-'.date('Ymd').'-'.strtoupper(Str::random(6));
    }
}

if (! function_exists('normalize_boolean')) {
    /**
     * Ubah nilai request (1/0/"true"/"false") menjadi boolean PHP.
     */
    function normalize_boolean(mixed $value): bool
    {
        return filter_var($value, FILTER_VALIDATE_BOOLEAN);
    }
}

if (! function_exists('slugify')) {
    /**
     * Buat slug yang bersih dari sebuah string, memakai Str::slug Laravel.
     */
    function slugify(string $value): string
    {
        return Str::slug($value);
    }
}
