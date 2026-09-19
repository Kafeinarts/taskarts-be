<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Buat tabel `drafts` untuk penyimpanan draf tulisan (Medium-style drafts).
     */
    public function up(): void
    {
        Schema::create('drafts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->index()->constrained()->nullOnDelete();
            $table->string('title')->nullable();
            $table->longText('content')->nullable();
            $table->string('type')->default('article');
            $table->string('status')->default('draft');
            $table->json('tags')->nullable();
            $table->unsignedInteger('read_time')->nullable();
            $table->string('canonical_url')->nullable();
            $table->boolean('is_published')->default(false);
            $table->timestamp('published_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index(['type', 'is_published']);
        });
    }

    /**
     * Hapus tabel `drafts` saat rollback.
     */
    public function down(): void
    {
        Schema::dropIfExists('drafts');
    }
};
