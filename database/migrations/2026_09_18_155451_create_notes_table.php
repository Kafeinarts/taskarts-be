<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Buat tabel `notes` untuk catatan tempel / catatan umum (sticky notes).
     */
    public function up(): void
    {
        Schema::create('notes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->index()->constrained()->nullOnDelete();
            $table->string('title')->nullable();
            $table->longText('content')->nullable();
            $table->string('color')->nullable();
            $table->string('category')->nullable();
            $table->json('tags')->nullable();
            $table->boolean('is_pinned')->default(false);
            $table->boolean('is_archived')->default(false);
            $table->timestamps();

            $table->index(['user_id', 'is_pinned']);
            $table->index(['user_id', 'is_archived']);
        });
    }

    /**
     * Hapus tabel `notes` saat rollback.
     */
    public function down(): void
    {
        Schema::dropIfExists('notes');
    }
};
