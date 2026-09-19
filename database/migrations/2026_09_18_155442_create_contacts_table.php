<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Buat tabel `contacts` untuk modul kontak / daftar kenalan.
     */
    public function up(): void
    {
        Schema::create('contacts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->index()->constrained()->nullOnDelete();
            $table->string('name');
            $table->string('company')->nullable();
            $table->string('role')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->text('address')->nullable();
            $table->string('website')->nullable();
            $table->json('tags')->nullable();
            $table->text('notes')->nullable();
            $table->boolean('is_favorite')->default(false);
            $table->string('avatar')->nullable();
            $table->timestamps();

            $table->index(['name', 'company']);
            $table->index(['user_id', 'is_favorite']);
        });
    }

    /**
     * Hapus tabel `contacts` saat rollback.
     */
    public function down(): void
    {
        Schema::dropIfExists('contacts');
    }
};
