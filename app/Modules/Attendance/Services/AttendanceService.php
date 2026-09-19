<?php

declare(strict_types=1);

namespace App\Modules\Attendance\Services;

use App\Modules\Attendance\Models\AttendanceLog;
use Illuminate\Support\Collection;

class AttendanceService
{
    public function logLogin(int $userId, ?string $ip = null, ?string $agent = null): AttendanceLog
    {
        return AttendanceLog::create([
            'user_id' => $userId,
            'type' => 'login',
            'logged_at' => now(),
            'ip_address' => $ip,
            'user_agent' => $agent,
        ]);
    }

    public function logLogout(int $userId, ?string $ip = null, ?string $agent = null): AttendanceLog
    {
        return AttendanceLog::create([
            'user_id' => $userId,
            'type' => 'logout',
            'logged_at' => now(),
            'ip_address' => $ip,
            'user_agent' => $agent,
        ]);
    }

    public function history(?int $userId = null, int $days = 30): Collection
    {
        $query = AttendanceLog::with('user:id,name,email,role')
            ->where('logged_at', '>=', now()->subDays($days))
            ->orderByDesc('logged_at');

        if ($userId) {
            $query->where('user_id', $userId);
        }

        return $query->get();
    }

    public function summary(int $days = 30): Collection
    {
        return AttendanceLog::selectRaw('user_id, DATE(logged_at) as date, MIN(CASE WHEN type = "login" THEN logged_at END) as first_login, MAX(CASE WHEN type = "logout" THEN logged_at END) as last_logout')
            ->with('user:id,name,email,role')
            ->where('logged_at', '>=', now()->subDays($days))
            ->groupBy('user_id', 'date')
            ->orderByDesc('date')
            ->orderBy('user_id')
            ->get();
    }
}
