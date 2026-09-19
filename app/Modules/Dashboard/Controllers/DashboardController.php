<?php

declare(strict_types=1);

namespace App\Modules\Dashboard\Controllers;

use App\Http\Controllers\Api\ApiController;
use App\Services\DashboardService;
use Illuminate\Http\JsonResponse;

/**
 * DashboardController
 *
 * Endpoint ringkasan agregat seluruh modul untuk dashboard utama aplikasi.
 */
class DashboardController extends ApiController
{
    /**
     * Tampilkan ringkasan menyeluruh semua modul.
     */
    public function overview(DashboardService $service): JsonResponse
    {
        return $this->ok($service->overview(), 'Ringkasan dashboard');
    }
}
