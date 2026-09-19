<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Buat tabel `mood_logs` untuk pencatatan mood / suasana hati.
     */
    public function up(): void
    {
        Schema::create('mood_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->index()->constrained()->nullOnDelete();
            $table->string('mood')->nullable();
            $table->string('mood_label')->nullable();
            $table->unsignedTinyInteger('score')->nullable();
            $table->text('note')->nullable();
            $table->dateTime('logged_at');
            $table->timestamps();

            $table->index(['user_id', 'logged_at']);
        });
    }

    /**
     * Hapus tabel `mood_logs` saat rollback.
     */
    public function down(): void
    {
        Schema::dropIfExists('mood_logs');
    }
};
