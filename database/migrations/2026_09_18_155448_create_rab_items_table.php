<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Buat tabel `rab_items` untuk item Rencana Anggaran Biaya (RAB).
     * Tipe `income` (pemasukan), `expense` (pengeluaran), atau `item` biasa.
     */
    public function up(): void
    {
        Schema::create('rab_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->index()->constrained()->nullOnDelete();
            $table->string('name');
            $table->string('type')->default('expense');
            $table->string('category')->nullable();
            $table->string('unit')->nullable();
            $table->decimal('quantity', 12, 2)->default(1);
            $table->decimal('unit_price', 15, 2)->default(0);
            $table->decimal('amount', 15, 2)->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['type', 'category']);
            $table->index(['user_id', 'created_at']);
        });
    }

    /**
     * Hapus tabel `rab_items` saat rollback.
     */
    public function down(): void
    {
        Schema::dropIfExists('rab_items');
    }
};
