<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Buat tabel `budgets` untuk anggaran bulanan/tahunan beserta realisasi pengeluaran.
     */
    public function up(): void
    {
        Schema::create('budgets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->index()->constrained()->nullOnDelete();
            $table->foreignId('category_id')->nullable()->index()->constrained('finance_categories')->nullOnDelete();
            $table->string('name');
            $table->decimal('amount', 15, 2)->default(0);
            $table->decimal('spent', 15, 2)->default(0);
            $table->string('period')->default('monthly');
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['period', 'is_active']);
            $table->index(['user_id', 'start_date']);
        });
    }

    /**
     * Hapus tabel `budgets` saat rollback.
     */
    public function down(): void
    {
        Schema::dropIfExists('budgets');
    }
};
