<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Modules\Settings\Models\FeatureSetting;
use Illuminate\Database\Seeder;

class FeatureSettingsSeeder extends Seeder
{
    /**
     * Seed semua fitur TaskArts dengan is_enabled = 1 (aktif).
     */
    public function run(): void
    {
        $features = [
            // Workspace & Proyek
            ['key' => 'dashboard', 'label' => 'Dashboard', 'description' => 'Ringkasan metrik produktivitas', 'group_name' => 'Workspace & Proyek', 'sort_order' => 1],
            ['key' => 'todo', 'label' => 'To-Do & Kanban', 'description' => 'Manajemen tugas dengan 6 mode tampilan', 'group_name' => 'Workspace & Proyek', 'sort_order' => 2],
            ['key' => 'project', 'label' => 'Proyek & Kontrak', 'description' => 'Manajemen proyek, milestone, dan kontrak', 'group_name' => 'Workspace & Proyek', 'sort_order' => 3],
            ['key' => 'calendar', 'label' => 'Kalender', 'description' => 'Jadwal dan agenda terintegrasi', 'group_name' => 'Workspace & Proyek', 'sort_order' => 4],
            ['key' => 'job-tracker', 'label' => 'Simpan Lamaran Kerja', 'description' => 'Pelamaran kerja & job tracking', 'group_name' => 'Workspace & Proyek', 'sort_order' => 5],
            ['key' => 'quick-capture', 'label' => 'Quick Capture', 'description' => 'Catatan cepat', 'group_name' => 'Workspace & Proyek', 'sort_order' => 6],

            // Keuangan
            ['key' => 'finance', 'label' => 'Keuangan', 'description' => 'Pelacak arus kas harian', 'group_name' => 'Keuangan', 'sort_order' => 10],
            ['key' => 'finance-cashflow', 'label' => 'Cash Flow Management', 'description' => 'Manajemen arus kas & proyeksi', 'group_name' => 'Keuangan', 'sort_order' => 11],
            ['key' => 'finance-ap-ar', 'label' => 'Hutang & Piutang', 'description' => 'Accounts Payable & Receivable', 'group_name' => 'Keuangan', 'sort_order' => 12],
            ['key' => 'finance-budgeting', 'label' => 'Anggaran & Forecasting', 'description' => 'Perencanaan anggaran', 'group_name' => 'Keuangan', 'sort_order' => 13],
            ['key' => 'finance-reports', 'label' => 'Laporan Finansial', 'description' => 'Neraca, laba/rugi, arus kas', 'group_name' => 'Keuangan', 'sort_order' => 14],
            ['key' => 'finance-expenses', 'label' => 'Kompensasi & Reimbursement', 'description' => 'Klaim pengeluaran staf', 'group_name' => 'Keuangan', 'sort_order' => 15],
            ['key' => 'finance-security', 'label' => 'Audit Keamanan Finansial', 'description' => 'Audit & keamanan data finansial', 'group_name' => 'Keuangan', 'sort_order' => 16],
            ['key' => 'rab', 'label' => 'RAB & Kas Kegiatan', 'description' => 'Rencana Anggaran Biaya', 'group_name' => 'Keuangan', 'sort_order' => 17],
            ['key' => 'invoice', 'label' => 'Invoice Generator', 'description' => 'Pembuat faktur profesional A4/A5', 'group_name' => 'Keuangan', 'sort_order' => 18],

            // Catatan & Dokumen
            ['key' => 'notes', 'label' => 'Sticky Notes', 'description' => 'Catatan tempel visual', 'group_name' => 'Catatan & Dokumen', 'sort_order' => 20],
            ['key' => 'code-notes', 'label' => 'Code Notes & Snippets', 'description' => 'Editor kode dengan syntax highlighting', 'group_name' => 'Catatan & Dokumen', 'sort_order' => 21],
            ['key' => 'diary', 'label' => 'Jurnal & Diary', 'description' => 'Jurnal harian dengan mood tracker', 'group_name' => 'Catatan & Dokumen', 'sort_order' => 22],
            ['key' => 'medium-draft', 'label' => 'Medium Draft Suite', 'description' => 'Draf tulisan ala Medium', 'group_name' => 'Catatan & Dokumen', 'sort_order' => 23],
            ['key' => 'surat', 'label' => 'Surat Builder', 'description' => 'Generator surat dinas & perjanjian', 'group_name' => 'Catatan & Dokumen', 'sort_order' => 24],
            ['key' => 'cv', 'label' => 'CV Builder Pro', 'description' => 'Pembuat CV berstandar ATS', 'group_name' => 'Catatan & Dokumen', 'sort_order' => 25],

            // Komunikasi & Kontak
            ['key' => 'contacts', 'label' => 'Kontak', 'description' => 'Manajemen kontak & integrasi Google', 'group_name' => 'Komunikasi & Kontak', 'sort_order' => 30],
            ['key' => 'team-collaboration', 'label' => 'Kolaborasi Tim', 'description' => 'Buletin, kanal, aset tim', 'group_name' => 'Komunikasi & Kontak', 'sort_order' => 31],
            ['key' => 'chat-ai', 'label' => 'Chat AI Assistant', 'description' => 'Asisten AI berbasis Gemini', 'group_name' => 'Komunikasi & Kontak', 'sort_order' => 32],

            // Produktivitas
            ['key' => 'habits', 'label' => 'Habit Tracker', 'description' => 'Pelacakan kebiasaan harian', 'group_name' => 'Produktivitas', 'sort_order' => 40],
            ['key' => 'mood', 'label' => 'Mood & Alarm', 'description' => 'Tracking suasana hati & alarm kerja', 'group_name' => 'Produktivitas', 'sort_order' => 41],
            ['key' => 'time-suite', 'label' => 'Time Suite', 'description' => 'Timer, stopwatch, pomodoro', 'group_name' => 'Produktivitas', 'sort_order' => 42],
            ['key' => 'productivity-insights', 'label' => 'Productivity Insights', 'description' => 'Analisis produktivitas', 'group_name' => 'Produktivitas', 'sort_order' => 43],

            // Media & Hiburan
            ['key' => 'videos', 'label' => 'Video Hub', 'description' => 'Galeri video', 'group_name' => 'Media & Hiburan', 'sort_order' => 50],
            ['key' => 'games', 'label' => 'Mini Games & Simulator 3D', 'description' => 'Game interaktif Three.js', 'group_name' => 'Media & Hiburan', 'sort_order' => 51],
            ['key' => 'selfie', 'label' => 'Kamera & Selfie', 'description' => 'Kamera kebahagiaan', 'group_name' => 'Media & Hiburan', 'sort_order' => 52],
            ['key' => 'custom-bingkai', 'label' => 'Motivation Frame', 'description' => 'Bingkai motivasi 3D', 'group_name' => 'Media & Hiburan', 'sort_order' => 53],

            // Sistem
            ['key' => 'storage', 'label' => 'Storage Manager', 'description' => 'Manajemen kapasitas memori', 'group_name' => 'Sistem', 'sort_order' => 60],
            ['key' => 'settings', 'label' => 'Pengaturan', 'description' => 'Tema, warna, preferensi', 'group_name' => 'Sistem', 'sort_order' => 61],
            ['key' => 'developer', 'label' => 'Developer Portfolio', 'description' => 'Portofolio pengembang', 'group_name' => 'Sistem', 'sort_order' => 62],
        ];

        foreach ($features as $feature) {
            FeatureSetting::updateOrCreate(
                ['key' => $feature['key']],
                array_merge($feature, ['is_enabled' => 1]),
            );
        }
    }
}
