<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Buat tabel `finance_categories` untuk kategori pemasukan/pengeluaran.
     */
    public function up(): void
    {
        Schema::create('finance_categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->index()->constrained()->nullOnDelete();
            $table->string('name');
            $table->string('type')->default('expense');
            $table->string('icon')->nullable();
            $table->string('color')->nullable();
            $table->timestamps();

            $table->index(['type', 'name']);
        });
    }

    /**
     * Hapus tabel `finance_categories` saat rollback.
     */
    public function down(): void
    {
        Schema::dropIfExists('finance_categories');
    }
};
