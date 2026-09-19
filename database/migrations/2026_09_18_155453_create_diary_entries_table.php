<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Buat tabel `diary_entries` untuk modul jurnal / buku harian.
     */
    public function up(): void
    {
        Schema::create('diary_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->index()->constrained()->nullOnDelete();
            $table->string('title')->nullable();
            $table->longText('content')->nullable();
            $table->string('mood')->nullable();
            $table->string('mood_label')->nullable();
            $table->string('weather')->nullable();
            $table->string('weather_label')->nullable();
            $table->string('location')->nullable();
            $table->string('category')->nullable();
            $table->json('tags')->nullable();
            $table->boolean('is_pinned')->default(false);
            $table->boolean('is_favorite')->default(false);
            $table->boolean('is_private')->default(false);
            $table->date('entry_date');
            $table->time('entry_time')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'entry_date']);
            $table->index(['user_id', 'is_favorite']);
        });
    }

    /**
     * Hapus tabel `diary_entries` saat rollback.
     */
    public function down(): void
    {
        Schema::dropIfExists('diary_entries');
    }
};
