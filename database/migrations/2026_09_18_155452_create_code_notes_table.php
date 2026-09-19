<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Buat tabel `code_notes` untuk menyimpan potongan kode / snippe.
     */
    public function up(): void
    {
        Schema::create('code_notes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->index()->constrained()->nullOnDelete();
            $table->string('title');
            $table->longText('content')->nullable();
            $table->string('language')->nullable();
            $table->json('tags')->nullable();
            $table->boolean('is_pinned')->default(false);
            $table->boolean('is_favorite')->default(false);
            $table->boolean('is_archived')->default(false);
            $table->timestamps();

            $table->index(['user_id', 'language']);
            $table->index(['user_id', 'is_favorite']);
        });
    }

    /**
     * Hapus tabel `code_notes` saat rollback.
     */
    public function down(): void
    {
        Schema::dropIfExists('code_notes');
    }
};
