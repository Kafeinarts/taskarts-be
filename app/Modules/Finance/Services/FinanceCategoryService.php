<?php

declare(strict_types=1);

namespace App\Modules\Finance\Services;

use App\Modules\Finance\Models\FinanceCategory;
use App\Services\BaseService;
use Illuminate\Support\Collection;

/**
 * FinanceCategoryService
 *
 * Service modul Finance untuk CRUD kategori pemasukan/pengeluaran.
 */
class FinanceCategoryService extends BaseService
{
    /**
     * Daftar kategori keuangan milik user, opsional filter menurut tipe.
     */
    public function index(?string $type = null): Collection
    {
        $query = $this->own(FinanceCategory::query());

        if ($type !== null) {
            $query->where('type', $type);
        }

        return $query->withCount('transactions')->orderBy('name')->get();
    }

    /**
     * Ambil satu kategori.
     */
    public function show(FinanceCategory $category): FinanceCategory
    {
        $category->loadCount('transactions');

        return $category;
    }

    /**
     * Buat kategori baru.
     *
     * @param  array<string,mixed>  $data
     */
    public function store(array $data): FinanceCategory
    {
        $data['user_id'] = $this->userId();

        return FinanceCategory::create($data);
    }

    /**
     * Perbarui kategori yang sudah ada.
     *
     * @param  array<string,mixed>  $data
     */
    public function update(FinanceCategory $category, array $data): FinanceCategory
    {
        $category->update($data);

        return $category->fresh();
    }

    /**
     * Hapus kategori (transaksi terkait jadi ber-kategori null).
     */
    public function destroy(FinanceCategory $category): void
    {
        $category->delete();
    }
}
