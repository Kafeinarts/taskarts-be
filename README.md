# TaskArts API — Backend Laravel

Laravel 13 + MySQL sebagai **single source of truth** untuk platform produktivitas TaskArts.
Seluruh endpoint dirancang **fully modular** per modul aplikasi, dengan autentikasi **Sanctum**,
validasi inline, envelope respons JSON konsisten, serta dukungan **ekspor Excel & PDF**.

---

## Ringkasan Arsitektur

```
app/
├── Http/
│   └── Controllers/Api/ApiController.php   # Controller dasar: helper ok(), paginated(), fail()
├── Helpers/
│   ├── helpers.php                          # Fungsi global (current_user_id, format_rupiah, dll.)
│   └── ApiResponder.php                     # Pembentuk envelope respons JSON
├── Models/User.php                          # Model user + Sanctum HasApiTokens
├── Modules/                                 # ________ MODUL (fully modular) ________
│   ├── Auth/                                #   auth: register, login, logout, me
│   ├── Dashboard/                           #   ringkasan lintas modul
│   ├── Settings/                            #   key-value settings per user
│   ├── Tasks/                               #   proyek & tugas
│   ├── Contacts/                            #   kontak
│   ├── Finance/                             #   kategori, transaksi, anggaran, invoice, AP/AR, RAB
│   ├── Habits/                              #   kebiasaan + log harian
│   ├── Notes/                               #   catatan, code notes, diary, drafts
│   ├── Planner/                             #   events, mood logs, work alarms
│   ├── Cv/                                  #   curriculum vitae (data JSON)
│   └── Reports/                             #   ekspor lintas modul (Excel & PDF)
├── Services/
│   ├── BaseService.php                      # own()/tags()/money()/userId() dipakai semua service
│   ├── DashboardService.php                 # agregator statistik dashboard
│   └── Export/                              # GenericTableExport, ExcelExportService, PdfExportService
database/
└── migrations/                              # 24 tabel (20 resource + infrastruktur Laravel/Sanctum)
routes/
└── api.php                                  # prefix /v1, glob require routes/api/*.php
    ├── auth.php dashboard.php settings.php tasks.php contacts.php finance.php
    ├── habits.php notes.php planner.php cv.php reports.php
```

Setiap modul berisi tiga lapisan yang saling terpisah:

| Lapisan | Lokasi | Peran |
|---|---|---|
| **Model** | `app/Modules/<Modul>/Models/` | Atribut `#[Fillable]`/`#[Hidden]`, `casts()`, relasi, accessor |
| **Service** | `app/Modules/<Modul>/Services/` | Seluruh logika bisnis; controller tetap tipis |
| **Controller** | `app/Modules/<Modul>/Controllers/` | Validasi request + memanggil service + membentuk respons |

Separasi ini membuat **logika bisnis modular & dapat diuji** tanpa bergantung pada HTTP layer.

---

## Tabel Database (20 Resource)

| Modul | Tabel |
|---|---|
| Settings | `settings` |
| Tasks | `projects`, `tasks` |
| Contacts | `contacts` |
| Finance | `finance_categories`, `finance_transactions`, `budgets`, `invoices`, `ap_ar_entries`, `rab_items` |
| Habits | `habits`, `habit_logs` |
| Notes | `notes`, `code_notes`, `diary_entries`, `drafts` |
| Planner | `events`, `mood_logs`, `work_alarms` |
| Cv | `cvs` |

Dukungan infrastruktur: `users`, `personal_access_tokens` (Sanctum), `cache`, `jobs`, dll.
Seluruh tabel resource memiliki kolom `user_id` (nullable, `nullOnDelete`) sehingga siap multi-user.

---

## Authentication (Sanctum)

| Method | Endpoint | Deskripsi |
|---|---|---|
| POST | `/api/v1/auth/register` | Daftar akun baru (menghasilkan token) |
| POST | `/api/v1/auth/login` | Login → mengembalikan `token` (rate limit 5/menit) |
| POST | `/api/v1/auth/logout` | Revoke seluruh token user |
| GET  | `/api/v1/auth/me` | Profil user yang sedang login |

Semua endpoint resource lain di bawah `auth:sanctum`. Header: `Authorization: Bearer <token>`.

---

## Format Respons (Envelope)

Setiap respons berstruktur sama (dibentuk `ApiResponder` / `ApiController`):

```json
{
  "success": true,
  "message": "Tugas dibuat",
  "data": { ... },
  "meta": { "pagination": ... }   // opsional
}
```

Error validasi: HTTP 422 dengan `data.errors` berisi array field → pesan.
Error 404/500 memakai penanganan global yang sudah diset JSON untuk `api/*`.

---

## Daftar Endpoint per Modul

Semua di bawah prefix **`/api/v1`**.

### Dashboard
- `GET /dashboard` — ringkasan tugas, proyek, keuangan, invoice, AP/AR, RAB, kontak, habit, mood, budget.

### Settings
- `GET /settings`, `GET /settings/group/{group}`, `GET /settings/{key}`
- `POST /settings/batch`, `POST /settings/{key}`, `DELETE /settings/{key}`

### Tasks
- `GET /projects`, `GET /projects/stats`, `POST /projects`, `GET|PUT|PATCH /projects/{project}`, `DELETE /projects/{project}`
- `GET /tasks`, `GET /tasks/stats`, `POST /tasks`, `GET|PUT|PATCH /tasks/{task}`, `DELETE /tasks/{task}`
- `POST /tasks/{task}/complete`, `POST /tasks/{task}/reopen`

### Contacts
- `GET /contacts`, `GET /contacts/stats`, `POST /contacts`, `GET|PUT|PATCH /contacts/{contact}`, `DELETE /contacts/{contact}`

### Finance (`/finance/...`)
- Kategori: `categories`
- Transaksi: `transactions` + `transactions/summary`, `transactions/cash-flow`
- Anggaran: `budgets` + `budgets/{id}/sync-spent`, `budgets/{id}/usage`
- Invoice: `invoices` + `invoices/stats`, `invoices/{id}/pay`, `invoices/{id}/pdf`
- AP/AR: `ap-ar` + `ap-ar/summary`, `ap-ar/{id}/settle`
- RAB: `rab` + `rab/summary`

### Habits
- `habits` + `habits/{id}/log` (POST catat), `habits/{id}/log` (DELETE batalkan)

### Notes
- `notes`, `code-notes`, `diary`, `drafts` (CRUD standar)

### Planner
- `events` + `events/upcoming`, `mood-logs` + `mood-logs/summary`, `work-alarms`

### Cv
- `cvs` + `cvs/{id}/default`

### Reports (Ekspor)
- `GET /reports/tasks/excel` · `GET /reports/tasks/pdf`
- `GET /reports/contacts/excel` · `GET /reports/contacts/pdf`
- `GET /reports/finance/excel` · `GET /reports/finance/pdf`
- `GET /reports/rab/excel`

---

## Ekspor Excel & PDF

- **Excel** via `maatwebsite/excel ^4.0` — `ExcelExportService::download()` menghasilkan `xlsx` dengan header yang diberi gaya.
- **PDF** via `barryvdh/laravel-dompdf ^3.1` — `PdfExportService::table()` (laporan tabel) dan `PdfExportService::invoice()` (faktur resmi).
  Template Blade: `resources/views/pdf/table.blade.php` dan `resources/views/pdf/invoice.blade.php`
  (font DejaVu Sans agar Rupiah/Roman terbaca benar).

---

## Panduan Menjalankan

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan serve            # http://127.0.0.1:8000/api/v1
```

### Akun default setelah `--seed`
```
email    : admin@taskarts.app
password : secret123
```

### Lingkungan Pengembangan
- PHP `8.4.25` (wajib ekstensi `gd` untuk dompdf), Composer `2.10.3`, MySQL `8`.
- Package tambahan: `laravel/sanctum`, `maatwebsite/excel`, `barryvdh/laravel-dompdf`, dev `laravel/boost`.

---

## Konvensi Kode

1. **PHPDoc pada setiap fungsi** public/protected untuk menjelaskan peran dan tipe data.
2. **Model** memakai atribut PHP 8 `#[Fillable([...])]` / `#[Hidden([...])]` (konvensi `User.php` yang sudah ada), bukan `protected $fillable`.
3. **Service** memakai `BaseService::own()` untuk scoping `user_id`, `tags()` untuk serialisasi tag JSON.
4. **Controller** tetap tipis: validasi inline `$request->validate()` lalu delegasi ke service.
5. **Routing** modular: `routes/api.php` mengumpulkan `routes/api/*.php` dengan `require` per modul, method `GET|POST|PUT|PATCH|DELETE` eksplisit (bukan `apiResource`).
6. **Bilingual** (Bahasa Indonesia) untuk pesan respons dan komentar, nama kode tetap English.
7. **Formatting**: `vendor/bin/pint` (gaya default Laravel) — jalankan setelah mengubah file PHP.
8. **Rate limiting** didefinisikan di `AppServiceProvider::boot()`: `api` (120/menit), `login` (5/menit), `auth` (10/menit).

---

## Struktur Footer Keseluruhan

```
back-end/
├── app/
│   ├── Http/Controllers/Api/ApiController.php
│   ├── Helpers/helpers.php, ApiResponder.php
│   ├── Models/User.php
│   ├── Modules/{Auth,Dashboard,Settings,Tasks,Contacts,Finance,Habits,Notes,Planner,Cv,Reports}/
│   │   └── {Models,Services,Controllers}/
│   └── Services/{BaseService,DashboardService}.php, Services/Export/*
├── bootstrap/app.php            # registrasi web+api routes, JSON exception render
├── config/{sanctum,dompdf}.php  # dipublish
├── database/
│   ├── migrations/              # 24 migrasi
│   └── seeders/DatabaseSeeder.php  # admin@taskarts.app
├── resources/views/pdf/         # template PDF
├── routes/api.php + routes/api/*.php
└── composer.json                # helpers.php di autoload.files
```