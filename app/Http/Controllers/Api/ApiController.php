<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Helpers\ApiResponder;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Routing\Controller as BaseController;

/**
 * ApiController
 *
 * Kontroler dasar seluruh endpoint JSON TaskArts. Menyediakan pintasan
 * respons sukses/error yang konsisten memakai App\Helpers\ApiResponder.
 */
abstract class ApiController extends BaseController
{
    use AuthorizesRequests;
    use ValidatesRequests;

    /**
     * Respons sukses standar.
     *
     * @param  mixed  $data  payload utama
     * @param  array<string,mixed>  $extra  data tambahan di luar envelope
     */
    protected function ok(mixed $data = [], string $message = 'OK', int $code = 200, array $extra = []): JsonResponse
    {
        return ApiResponder::success($data, $message, $code, $extra);
    }

    /**
     * Respons sukses ber-pagination.
     */
    protected function paginated(mixed $data, LengthAwarePaginator $paginator, string $message = 'OK'): JsonResponse
    {
        return ApiResponder::paginated($data, $paginator, $message);
    }

    /**
     * Respons error terstruktur.
     *
     * @param  mixed  $errors  detail error validasi (opsional)
     */
    protected function fail(string $message = 'Terjadi kesalahan', int $code = 422, mixed $errors = null): JsonResponse
    {
        return ApiResponder::error($message, $code, $errors);
    }
}
