<?php

declare(strict_types=1);

namespace App\Services;

use App\Modules\Contacts\Services\ContactService;
use App\Modules\Finance\Services\ApArService;
use App\Modules\Finance\Services\BudgetService;
use App\Modules\Finance\Services\FinanceTransactionService;
use App\Modules\Finance\Services\InvoiceService;
use App\Modules\Finance\Services\RabService;
use App\Modules\Habits\Services\HabitService;
use App\Modules\Planner\Services\MoodLogService;
use App\Modules\Tasks\Services\ProjectService;
use App\Modules\Tasks\Services\TaskService;

/**
 * DashboardService
 *
 * Agregator ringkasan lintas modul untuk endpoint dashboard utama.
 */
class DashboardService
{
    /**
     * Injeksi service semua modul via constructor.
     */
    public function __construct(
        private readonly TaskService $tasks,
        private readonly ProjectService $projects,
        private readonly FinanceTransactionService $finance,
        private readonly InvoiceService $invoices,
        private readonly ApArService $apAr,
        private readonly RabService $rab,
        private readonly HabitService $habits,
        private readonly MoodLogService $mood,
        private readonly ContactService $contacts,
        private readonly BudgetService $budgets,
    ) {}

    /**
     * Susun ringkasan menyeluruh untuk halaman dashboard.
     *
     * @return array<string,mixed>
     */
    public function overview(): array
    {
        return [
            'tasks' => $this->tasks->stats(),
            'projects' => $this->projects->stats(),
            'finance' => $this->finance->summary(),
            'invoices' => $this->invoices->stats(),
            'ap_ar' => $this->apAr->summary(),
            'rab' => $this->rab->summary(),
            'contacts' => $this->contacts->stats(),
            'habits' => $this->habitCounts(),
            'mood' => $this->mood->summary(),
            'budgets_active' => $this->budgets->index(true)->count(),
            'generated_at' => now()->toIso8601String(),
        ];
    }

    /**
     * Hitung jumlah kebiasaan aktif dan total streak gabungan.
     *
     * @return array<string,int>
     */
    private function habitCounts(): array
    {
        $activeHabits = $this->habits->index(true);

        return [
            'total' => $activeHabits->count(),
            'total_streak' => (int) $activeHabits->sum('streak'),
        ];
    }
}
