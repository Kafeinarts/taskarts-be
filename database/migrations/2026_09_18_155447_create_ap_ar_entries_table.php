<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Buat tabel `ap_ar_entries` untuk entri hutang (payable) & piutang (receivable).
     */
    public function up(): void
    {
        Schema::create('ap_ar_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->index()->constrained()->nullOnDelete();
            $table->string('type')->default('receivable');
            $table->string('contact_name')->nullable();
            $table->text('description')->nullable();
            $table->decimal('amount', 15, 2)->default(0);
            $table->decimal('paid_amount', 15, 2)->default(0);
            $table->date('due_date')->nullable();
            $table->string('status')->default('open');
            $table->timestamp('settled_at')->nullable();
            $table->timestamps();

            $table->index(['type', 'status']);
            $table->index(['user_id', 'due_date']);
        });
    }

    /**
     * Hapus tabel `ap_ar_entries` saat rollback.
     */
    public function down(): void
    {
        Schema::dropIfExists('ap_ar_entries');
    }
};
