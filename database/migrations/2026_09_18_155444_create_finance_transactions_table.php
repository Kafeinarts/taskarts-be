<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Buat tabel `finance_transactions` untuk transaksi pemasukan & pengeluaran.
     */
    public function up(): void
    {
        Schema::create('finance_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->index()->constrained()->nullOnDelete();
            $table->foreignId('category_id')->nullable()->index()->constrained('finance_categories')->nullOnDelete();
            $table->string('type')->default('expense');
            $table->decimal('amount', 15, 2)->default(0);
            $table->date('date');
            $table->text('description')->nullable();
            $table->string('payment_method')->nullable();
            $table->string('status')->default('completed');
            $table->string('reference')->nullable();
            $table->json('attachments')->nullable();
            $table->timestamps();

            $table->index(['type', 'date']);
            $table->index(['user_id', 'category_id']);
        });
    }

    /**
     * Hapus tabel `finance_transactions` saat rollback.
     */
    public function down(): void
    {
        Schema::dropIfExists('finance_transactions');
    }
};
