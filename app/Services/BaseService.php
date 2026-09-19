<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Database\Eloquent\Builder;

/**
 * BaseService
 *
 * Layanan dasar yang dipakai seluruh service modul. Berisi utilitas umum,
 * utamanya untuk memfilter data sesuai user yang sedang login
 * (multi-user ready) dan normalisasi input umum.
 */
abstract class BaseService
{
    /**
     * Ambil ID user yang sedang login (nullable).
     */
    protected function userId(): ?int
    {
        return current_user_id();
    }

    /**
     * Terapkan filter kepemilikan `user_id` ke query bila user sedang login.
     */
    protected function own(Builder $query): Builder
    {
        if ($this->userId() !== null) {
            $query->where('user_id', $this->userId());
        }

        return $query;
    }

    /**
     * Normalisasi input tags menjadi array string.
     */
    protected function tags(mixed $value): array
    {
        return normalize_tags($value);
    }

    /**
     * Normalisasi nilai desimal kuncian menjadi float 2 desimal.
     */
    protected function money(mixed $value): float
    {
        return money_value($value);
    }
}
