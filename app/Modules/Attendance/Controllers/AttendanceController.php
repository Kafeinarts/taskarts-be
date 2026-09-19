<?php

declare(strict_types=1);

namespace App\Modules\Attendance\Controllers;

use App\Http\Controllers\Api\ApiController;
use App\Modules\Attendance\Services\AttendanceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AttendanceController extends ApiController
{
    public function index(Request $request, AttendanceService $service): JsonResponse
    {
        $days = (int) $request->query('days', 30);
        $userId = $request->query('user_id') ? (int) $request->query('user_id') : null;

        return $this->ok($service->history($userId, $days), 'Riwayat absensi');
    }

    public function summary(AttendanceService $service): JsonResponse
    {
        return $this->ok($service->summary(30), 'Ringkasan absensi');
    }

    public function store(Request $request, AttendanceService $service): JsonResponse
    {
        $data = $request->validate([
            'type' => 'required|in:login,logout',
        ]);

        $log = $data['type'] === 'login'
            ? $service->logLogin($request->user()->id, $request->ip(), $request->userAgent())
            : $service->logLogout($request->user()->id, $request->ip(), $request->userAgent());

        return $this->ok($log, 'Absensi tercatat', 201);
    }
}
