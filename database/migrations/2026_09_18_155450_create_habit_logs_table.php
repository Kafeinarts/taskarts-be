<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Buat tabel `habit_logs` untuk log harian pencapaian kebiasaan.
     * Satu baris per id habit + tanggal (unique).
     */
    public function up(): void
    {
        Schema::create('habit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('habit_id')->index()->constrained()->cascadeOnDelete();
            $table->date('log_date');
            $table->decimal('value', 12, 2)->default(1);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['habit_id', 'log_date']);
            $table->index(['log_date']);
        });
    }

    /**
     * Hapus tabel `habit_logs` saat rollback.
     */
    public function down(): void
    {
        Schema::dropIfExists('habit_logs');
    }
};
