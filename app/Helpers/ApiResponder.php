<?php

declare(strict_types=1);

namespace App\Helpers;

use Illuminate\Http\JsonResponse;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * ApiResponder
 *
 * Helper konsisten untuk membungkus seluruh respons JSON API TaskArts.
 * Semua endpoint memakai bentuk envelope berikut:
 *
 *   {
 *     "success": true,
 *     "message": "...",
 *     "data": ...,
 *     "meta": ...        (opsional, mis. pagination)
 *   }
 */
class ApiResponder
{
    /**
     * Tanggapan sukses dasar.
     *
     * @param  mixed  $data  payload utama
     * @param  string  $message  pesan singkat
     * @param  int  $code  kode HTTP (default 200)
     * @param  array<string,mixed>  $extra  data tambahan di luar envelope
     */
    public static function success(mixed $data = [], string $message = 'OK', int $code = 200, array $extra = []): JsonResponse
    {
        $payload = [
            'success' => true,
            'message' => $message,
            'data' => $data,
        ];

        return response()->json(array_merge($payload, $extra), $code);
    }

    /**
     * Tanggapan sukses disertai metadata pagination (processed data).
     *
     * @param  mixed  $data  koleksi item pada halaman ini
     * @param  LengthAwarePaginator<mixed>  $paginator  paginator asli
     */
    public static function paginated(mixed $data, LengthAwarePaginator $paginator, string $message = 'OK'): JsonResponse
    {
        $payload = [
            'success' => true,
            'message' => $message,
            'data' => $data,
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
                'from' => $paginator->firstItem(),
                'to' => $paginator->lastItem(),
            ],
        ];

        return response()->json($payload, 200);
    }

    /**
     * Tanggapan error terstruktur.
     *
     * @param  mixed  $errors  detail error validasi (opsional)
     */
    public static function error(string $message = 'Terjadi kesalahan', int $code = 422, mixed $errors = null): JsonResponse
    {
        $payload = [
            'success' => false,
            'message' => $message,
            'data' => null,
        ];

        if ($errors !== null) {
            $payload['errors'] = $errors;
        }

        return response()->json($payload, $code);
    }
}
