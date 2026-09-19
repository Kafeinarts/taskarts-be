<?php

declare(strict_types=1);

namespace App\Modules\Cv\Services;

use App\Modules\Cv\Models\Cv;
use App\Services\BaseService;
use Illuminate\Support\Collection;

/**
 * CvService
 *
 * Service modul Cv untuk CRUD data CV/resume (berbasis JSON) dan
 * penentuan CV default.
 */
class CvService extends BaseService
{
    /**
     * Daftar CV milik user, opsional filter default.
     */
    public function index(?bool $default = null): Collection
    {
        $query = $this->own(Cv::query());

        if ($default !== null) {
            $query->where('is_default', $default);
        }

        return $query->orderByDesc('is_default')->latest()->get();
    }

    /**
     * Ambil satu CV.
     */
    public function show(Cv $cv): Cv
    {
        return $cv;
    }

    /**
     * Buat CV baru. Bila dikirim flag default, reset default CV lain.
     *
     * @param  array<string,mixed>  $data
     */
    public function store(array $data): Cv
    {
        $data['user_id'] = $this->userId();

        if (normalize_boolean($data['is_default'] ?? false)) {
            $this->resetDefault($data['user_id']);
        }

        $cv = Cv::create($data);

        return $cv->fresh();
    }

    /**
     * Perbarui CV yang sudah ada.
     *
     * @param  array<string,mixed>  $data
     */
    public function update(Cv $cv, array $data): Cv
    {
        if (normalize_boolean($data['is_default'] ?? false)) {
            $this->resetDefault($cv->user_id);
            $data['is_default'] = true;
        }

        $cv->update($data);

        return $cv->fresh();
    }

    /**
     * Hapus CV.
     */
    public function destroy(Cv $cv): void
    {
        $cv->delete();
    }

    /**
     * Set satu CV menjadi default (reset yang lain).
     */
    public function setDefault(Cv $cv): Cv
    {
        $this->resetDefault($cv->user_id);
        $cv->update(['is_default' => true]);

        return $cv->fresh();
    }

    /**
     * Nonaktifkan flag default pada seluruh CV milik user.
     */
    protected function resetDefault(?int $userId): void
    {
        Cv::query()
            ->where('user_id', $userId)
            ->update(['is_default' => false]);
    }
}
