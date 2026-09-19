<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Buat tabel `cvs` untuk menyimpan data CV / resume berbasis JSON.
     */
    public function up(): void
    {
        Schema::create('cvs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->index()->constrained()->nullOnDelete();
            $table->string('name')->default('Untitled CV');
            $table->json('data')->nullable();
            $table->string('selected_template')->nullable();
            $table->string('custom_color')->nullable();
            $table->boolean('is_default')->default(false);
            $table->timestamps();

            $table->index(['user_id', 'is_default']);
        });
    }

    /**
     * Hapus tabel `cvs` saat rollback.
     */
    public function down(): void
    {
        Schema::dropIfExists('cvs');
    }
};
